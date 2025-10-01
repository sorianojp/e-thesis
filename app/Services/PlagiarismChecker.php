<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;

class PlagiarismChecker
{
    private Parser $parser;

    public function __construct(?Parser $parser = null)
    {
        $this->parser = $parser ?? new Parser();
    }

    /**
     * Run a Winston plagiarism scan against the provided thesis PDF.
     *
     * @return array{score: int|null, response: array<string, mixed>}|null
     */
    public function scan(UploadedFile $pdf): ?array
    {
        $apiKey = config('services.winston.key');
        if (!$apiKey) {
            return null;
        }

        $text = $this->extractText($pdf);
        if ($text === '') {
            return null;
        }

        $payload = [
            'language' => config('services.winston.language', 'en'),
            'country' => config('services.winston.country', 'us'),
            'text' => $text,
        ];

        $url = $this->resolveEndpoint();

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout((int) config('services.winston.timeout', 45))
                ->post($url, $payload);
        } catch (\Throwable $e) {
            Log::warning('Plagiarism API request failed', [
                'exception' => $e,
            ]);

            return null;
        }

        if ($response->failed()) {
            Log::warning('Plagiarism API responded with error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        $data = $response->json();

        return [
            'score' => data_get($data, 'result.score'),
            'response' => $data,
        ];
    }

    private function extractText(UploadedFile $pdf): string
    {
        try {
            $document = $this->parser->parseFile($pdf->getRealPath());
        } catch (\Throwable $e) {
            Log::warning('Unable to parse thesis PDF for plagiarism scan', [
                'exception' => $e,
            ]);

            return '';
        }

        $text = trim(Str::of($document->getText() ?? '')
            ->replaceMatches('/\s+/u', ' ')
            ->toString());

        if ($text === '') {
            return '';
        }

        $max = (int) config('services.winston.max_characters', 20000);

        return mb_substr($text, 0, $max);
    }

    private function resolveEndpoint(): string
    {
        $endpoint = config('services.winston.endpoint');
        if (is_string($endpoint) && Str::startsWith($endpoint, ['http://', 'https://'])) {
            return $endpoint;
        }

        $base = rtrim((string) config('services.winston.base_uri', 'https://api.gowinston.ai/v2'), '/');
        $endpoint = $endpoint ?: 'plagiarism';

        return $base . '/' . ltrim($endpoint, '/');
    }
}
