<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Thesis;
use App\Services\CopyleaksClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CopyleaksWebhookController extends Controller
{
    public function __construct(protected CopyleaksClient $copyleaks)
    {
    }

    public function status(Request $request)
    {
        $payload = $request->all();
        $scanId = $this->extractScanId($payload);
        $status = $this->normalizeStatus($payload['status'] ?? $payload['event'] ?? $payload['action'] ?? null);

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

            if ($this->copyleaks->requestExport($scanId)) {
                Thesis::where('plagiarism_scan_id', $scanId)->update([
                    'plagiarism_status' => 'export_requested',
                ]);
            }
        }

        Log::info('Copyleaks completed webhook', [
            'scan_id' => $scanId,
            'score' => $score,
            'payload' => $payload,
        ]);

        return response()->json(['received' => true]);
    }

    public function export(Request $request)
    {
        $payload = $request->all();
        $scanId = $this->extractScanId($payload);
        $exportStatus = $this->normalizeStatus($payload['status'] ?? $payload['event'] ?? 'export_ready');

        if ($scanId) {
            Thesis::where('plagiarism_scan_id', $scanId)->update([
                'plagiarism_status' => $exportStatus,
            ]);
        }

        Log::info('Copyleaks export webhook', [
            'scan_id' => $scanId,
            'status' => $exportStatus,
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

    protected function normalizeStatus(null|int|string $status): string
    {
        $map = [
            0 => 'processing',
            1 => 'completed',
            2 => 'error',
            'queued' => 'processing',
            'finished' => 'completed',
            'completed' => 'completed',
            'error' => 'error',
            'failed' => 'error',
            'export_ready' => 'export_ready',
        ];

        if (is_numeric($status)) {
            $status = (int) $status;
        }

        return $map[$status] ?? (is_string($status) ? strtolower($status) : 'processing');
    }
}
