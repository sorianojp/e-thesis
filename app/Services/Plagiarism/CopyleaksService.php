<?php

namespace App\Services\Plagiarism;

use App\Models\Thesis;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class CopyleaksService
{
    public function submitThesisForScan(Thesis $thesis, UploadedFile $file): void
    {
        if (! $this->hasCredentials()) {
            Log::warning('Skipping Copyleaks submission: credentials missing.');
            $thesis->forceFill([
                'plagiarism_status' => 'skipped',
            ])->save();

            return;
        }

        $scanId = (string) Str::uuid();
        $token = Str::random(64);

        Cache::put($this->tokenCacheKey($scanId), $token, now()->addHours(2));

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'pdf');
        $filename = $file->getClientOriginalName() ?: "{$scanId}.{$extension}";

        $payload = [
            'base64' => base64_encode(file_get_contents($file->getRealPath())),
            'filename' => $filename,
            'properties' => [
                'action' => 0,
                'includeHtml' => true,
                'sandbox' => config('copyleaks.sandbox', false),
                'webhooks' => [
                    'status' => $this->buildWebhookUrl($token),
                ],
            ],
        ];

        try {
            Http::withToken($this->authenticate())
                ->acceptJson()
                ->put("https://api.copyleaks.com/v3/scans/submit/file/{$scanId}", $payload)
                ->throw();

            $thesis->forceFill([
                'plagiarism_scan_id' => $scanId,
                'plagiarism_status' => 'submitted',
            ])->save();
        } catch (Throwable $e) {
            Log::error('Copyleaks submission failed.', [
                'scan_id' => $scanId,
                'thesis_id' => $thesis->id,
                'message' => $e->getMessage(),
            ]);

            Cache::forget($this->tokenCacheKey($scanId));

            $thesis->forceFill([
                'plagiarism_status' => 'failed',
            ])->save();
        }
    }

    public function verifyToken(string $scanId, string $token): bool
    {
        return Cache::get($this->tokenCacheKey($scanId)) === $token;
    }

    public function forgetToken(string $scanId): void
    {
        Cache::forget($this->tokenCacheKey($scanId));
    }

    protected function authenticate(): string
    {
        $response = Http::baseUrl('https://id.copyleaks.com/v3/')
            ->acceptJson()
            ->post('account/login/api', [
                'email' => config('copyleaks.email'),
                'key' => config('copyleaks.key'),
            ]);

        $response->throw();

        $token = $response->json('access_token');

        if (! $token) {
            throw new RuntimeException('Copyleaks authentication failed: missing access token.');
        }

        return $token;
    }

    protected function buildWebhookUrl(string $token): string
    {
        $path = "/api/plagiarism/webhook/{STATUS}/{$token}";

        $base = config('copyleaks.webhook_base');

        return $base ? rtrim($base, '/') . $path : URL::to($path);
    }

    protected function tokenCacheKey(string $scanId): string
    {
        return "copyleaks-token:{$scanId}";
    }

    protected function hasCredentials(): bool
    {
        return filled(config('copyleaks.email')) && filled(config('copyleaks.key'));
    }
}
