<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Thesis;
use App\Services\Plagiarism\CopyleaksService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CopyleaksWebhookController extends Controller
{
    public function __construct(private CopyleaksService $copyleaks)
    {
    }

    public function __invoke(Request $request, string $status, string $token): JsonResponse
    {
        $scanId = $request->input('scannedDocument.scanId');

        if (! $scanId) {
            return response()->json(['message' => 'Missing scan ID.'], 422);
        }

        if (! $this->copyleaks->verifyToken($scanId, $token)) {
            return response()->json(['message' => 'Invalid webhook token.'], 403);
        }

        $thesis = Thesis::query()->where('plagiarism_scan_id', $scanId)->first();

        if (! $thesis) {
            return response()->json(['message' => 'Thesis not found for scan ID.'], 404);
        }

        $payload = $request->all();
        $now = Carbon::now();

        switch ($status) {
            case 'completed':
                $score = $this->extractScore($payload);

                $thesis->forceFill([
                    'plagiarism_status' => 'completed',
                    'plagiarism_score' => $score,
                    'plagiarism_report' => $payload,
                    'plagiarism_checked_at' => $now,
                ])->save();

                $this->copyleaks->forgetToken($scanId);
                break;
            case 'error':
            case 'failed':
                $thesis->forceFill([
                    'plagiarism_status' => 'failed',
                    'plagiarism_report' => $payload,
                    'plagiarism_checked_at' => $now,
                ])->save();

                $this->copyleaks->forgetToken($scanId);
                break;
            default:
                $thesis->forceFill([
                    'plagiarism_status' => $status,
                ])->save();
                break;
        }

        return response()->json(['ok' => true]);
    }

    private function extractScore(array $payload): ?float
    {
        $candidates = [
            data_get($payload, 'results.score.percent'),
            data_get($payload, 'results.score.value'),
            data_get($payload, 'results.score'),
            data_get($payload, 'results.summary.percent'),
            data_get($payload, 'scannedDocument.score.percent'),
            data_get($payload, 'scannedDocument.score.value'),
        ];

        foreach ($candidates as $candidate) {
            if (is_numeric($candidate)) {
                return round((float) $candidate, 2);
            }
        }

        $max = null;
        $this->collectScores($payload, $max);

        return $max !== null ? round($max, 2) : null;
    }

    private function collectScores(mixed $value, ?float &$max): void
    {
        if (! is_array($value)) {
            return;
        }

        foreach ($value as $key => $item) {
            if (is_array($item)) {
                $this->collectScores($item, $max);
                continue;
            }

            if ($this->looksLikeScore($key, $item)) {
                $score = $this->normaliseScore($item);
                $max = $max === null ? $score : max($max, $score);
            }
        }
    }

    private function looksLikeScore(mixed $key, mixed $value): bool
    {
        if (! is_string($key) || ! is_numeric($value)) {
            return false;
        }

        $key = strtolower($key);

        return str_contains($key, 'score')
            || str_contains($key, 'percent')
            || str_contains($key, 'similarity');
    }

    private function normaliseScore(mixed $value): float
    {
        $numeric = (float) $value;

        if ($numeric <= 1.0) {
            return $numeric * 100;
        }

        return $numeric;
    }
}
