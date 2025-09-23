<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Thesis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CopyleaksWebhookController extends Controller
{
    public function status(Request $request)
    {
        $payload = $request->all();
        $scanId = $this->extractScanId($payload);
        $status = $payload['status'] ?? $payload['event'] ?? $payload['action'] ?? 'processing';

        if ($scanId) {
            Thesis::where('plagiarism_scan_id', $scanId)->update([
                'plagiarism_status' => $status,
            ]);
        }

        Log::info('Copyleaks status webhook', [
            'scan_id' => $scanId,
            'status' => $status,
            'payload' => $payload,
        ]);

        return response()->json(['received' => true]);
    }

    public function completed(Request $request)
    {
        $payload = $request->all();
        $scanId = $this->extractScanId($payload);
        $score = $this->extractScore($payload);

        if ($scanId) {
            $update = [
                'plagiarism_status' => 'completed',
            ];

            if ($score !== null) {
                $update['plagiarism_score'] = $score;
            }

            Thesis::where('plagiarism_scan_id', $scanId)->update($update);
        }

        Log::info('Copyleaks completed webhook', [
            'scan_id' => $scanId,
            'score' => $score,
            'payload' => $payload,
        ]);

        return response()->json(['received' => true]);
    }

    protected function extractScanId(array $payload): ?string
    {
        return $payload['scanId']
            ?? data_get($payload, 'scan.scanId')
            ?? data_get($payload, 'result.scanId')
            ?? data_get($payload, 'scannedDocument.scanId')
            ?? null;
    }

    protected function extractScore(array $payload): ?float
    {
        $candidates = [
            data_get($payload, 'summary.score'),
            data_get($payload, 'summary.similarity'),
            data_get($payload, 'result.score'),
            data_get($payload, 'result.similarity'),
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
}
