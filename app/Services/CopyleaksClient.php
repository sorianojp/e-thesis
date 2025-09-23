<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class CopyleaksClient
{
    protected string $identityUrl;
    protected string $apiUrl;

    public function __construct()
    {
        $this->identityUrl = rtrim(config('services.copyleaks.identity_url', 'https://id.copyleaks.com'), '/');
        $this->apiUrl = rtrim(config('services.copyleaks.api_url', 'https://api.copyleaks.com'), '/');
    }

    public function submitScan(UploadedFile $file, array $metadata = []): array
    {
        if (!$this->credentialsAvailable()) {
            return [
                'scan_id' => null,
                'score' => null,
                'status' => 'skipped',
            ];
        }

        $token = $this->getAccessToken();

        if (!$token) {
            return [
                'scan_id' => null,
                'score' => null,
                'status' => 'authentication_failed',
            ];
        }

        $scanId = (string) Str::uuid();
        $timeout = (int) config('services.copyleaks.timeout', 30);

        try {
            $contents = file_get_contents($file->getRealPath());
        } catch (Throwable $e) {
            report($e);

            return [
                'scan_id' => null,
                'score' => null,
                'status' => 'read_failed',
            ];
        }

        $properties = array_filter([
            'sandbox' => filter_var(config('services.copyleaks.sandbox', false), FILTER_VALIDATE_BOOLEAN),
            'pdf' => [
                'create' => true,
            ],
            'metadata' => array_filter($metadata),
        ]);

        if ($webhooks = $this->buildWebhookConfig()) {
            $properties['webhooks'] = $webhooks;
        }

        $payload = [
            'base64' => base64_encode($contents),
            'filename' => $file->getClientOriginalName() ?: 'thesis.pdf',
            'properties' => $properties,
        ];

        $submitUrl = sprintf('%s/v3/scans/submit/file/%s', $this->apiUrl, $scanId);

        try {
            $response = Http::withToken($token)
                ->timeout($timeout)
                ->acceptJson()
                ->put($submitUrl, $payload);
        } catch (Throwable $e) {
            report($e);

            return [
                'scan_id' => null,
                'score' => null,
                'status' => 'submission_failed',
            ];
        }

        if (!$response->successful()) {
            logger()->warning('Copyleaks submission failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'scan_id' => null,
                'score' => null,
                'status' => 'submission_failed',
            ];
        }

        $result = $this->attemptImmediateScore($token, $scanId, $timeout);

        return [
            'scan_id' => $scanId,
            'score' => $result['score'],
            'status' => $result['status'],
        ];
    }

    public function requestExport(string $scanId, ?array $formats = null): ?string
    {
        $webhookUrl = $this->buildExportWebhookUrl();
        $formats = $formats ?? config('services.copyleaks.export_formats');

        if (!$webhookUrl || empty($formats)) {
            return null;
        }

        $token = $this->getAccessToken();

        if (!$token) {
            return null;
        }

        $timeout = (int) config('services.copyleaks.timeout', 30);
        $exportId = (string) Str::uuid();

        $payload = [
            'exportId' => $exportId,
            'webhook' => [
                'method' => 'POST',
                'url' => $webhookUrl,
                'headers' => new \stdClass(),
            ],
            'formats' => $formats,
        ];

        $exportUrl = sprintf('%s/v3/exports/%s/submit', $this->apiUrl, $scanId);

        try {
            $response = Http::withToken($token)
                ->timeout($timeout)
                ->acceptJson()
                ->post($exportUrl, $payload);

            if ($response->successful()) {
                return $exportId;
            }

            logger()->warning('Copyleaks export request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (Throwable $e) {
            report($e);
        }

        return null;
    }

    protected function attemptImmediateScore(string $token, string $scanId, int $timeout): array
    {
        $attempts = max(1, (int) config('services.copyleaks.poll_attempts', 5));
        $interval = max(1, (int) config('services.copyleaks.poll_interval', 5));

        for ($i = 0; $i < $attempts; $i++) {
            if ($i > 0) {
                sleep($interval);
            }

            $resultUrlCandidates = [
                sprintf('%s/v3/scans/%s/result', $this->apiUrl, $scanId),
                sprintf('%s/v3/scans/%s/summary', $this->apiUrl, $scanId),
            ];

            foreach ($resultUrlCandidates as $resultUrl) {
                try {
                    $response = Http::withToken($token)
                        ->timeout($timeout)
                        ->acceptJson()
                        ->get($resultUrl);
                } catch (Throwable $e) {
                    report($e);
                    continue;
                }

                if ($response->status() === 404) {
                    continue;
                }

                if (!$response->successful()) {
                    logger()->info('Copyleaks result not ready', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'url' => $resultUrl,
                    ]);
                    continue;
                }

                $score = $this->extractScore($response->json());
                if ($score !== null) {
                    return [
                        'score' => $score,
                        'status' => 'completed',
                    ];
                }
            }
        }

        return [
            'score' => null,
            'status' => 'processing',
        ];
    }

    protected function extractScore(?array $payload): ?float
    {
        if (!is_array($payload)) {
            return null;
        }

        $candidates = [
            data_get($payload, 'summary.score'),
            data_get($payload, 'summary.similarity'),
            data_get($payload, 'results.score'),
            data_get($payload, 'results.similarity'),
            data_get($payload, 'similarity.score'),
        ];

        foreach ($candidates as $value) {
            if (is_numeric($value)) {
                $float = (float) $value;

                return $float <= 1 ? $float * 100 : $float;
            }
        }

        return null;
    }

    protected function credentialsAvailable(): bool
    {
        return (bool) (config('services.copyleaks.email') && config('services.copyleaks.api_key'));
    }

    protected function buildWebhookConfig(): ?array
    {
        $base = config('services.copyleaks.webhook_base');

        if (!$base) {
            return null;
        }

        $base = rtrim($base, '/');

        return [
            'status' => $base . '/status',
            'completed' => $base . '/completed',
        ];
    }

    protected function buildExportWebhookUrl(): ?string
    {
        $base = config('services.copyleaks.webhook_base');

        return $base ? rtrim($base, '/') . '/export' : null;
    }

    protected function getAccessToken(): ?string
    {
        $cacheKey = 'copyleaks.access_token';

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $email = config('services.copyleaks.email');
        $apiKey = config('services.copyleaks.api_key');
        $timeout = (int) config('services.copyleaks.timeout', 30);

        try {
            $response = Http::timeout($timeout)
                ->acceptJson()
                ->post($this->identityUrl . '/v3/account/login/api', [
                    'email' => $email,
                    'key' => $apiKey,
                ]);
        } catch (Throwable $e) {
            report($e);

            return null;
        }

        if (!$response->successful()) {
            logger()->warning('Copyleaks authentication failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        $token = $response->json('access_token');
        $expiresIn = (int) ($response->json('expires_in') ?? 1800);

        if (!$token) {
            return null;
        }

        $ttlSeconds = max(60, $expiresIn - 60);
        Cache::put($cacheKey, $token, now()->addSeconds($ttlSeconds));

        return $token;
    }
}
