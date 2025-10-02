<?php

namespace App\Http\Controllers;

use App\Models\Thesis;
use App\Models\ThesisTitle;
use App\Services\PlagiarismChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ThesisController extends Controller
{
    public function store(Request $req, ThesisTitle $thesisTitle)
    {
        abort_unless($req->user()->isStudent() && $thesisTitle->user_id === $req->user()->id, 403);

        $req->validate([
            'thesis_pdf' => [
                'required',
                'file',
                'mimes:pdf',
                'mimetypes:application/pdf,application/x-pdf',
                'max:40960',
            ],
        ]);

        $userId = $req->user()->id;
        $prefix = trim((string) env('DO_SPACES_FOLDER', ''), '/');
        $basePath = $prefix ? "{$prefix}/" : '';
        $thesisDir = "{$basePath}theses/{$userId}";

        $slug = str($thesisTitle->title)->slug()->limit(80, '');
        $timestamp = now()->format('Ymd_His');
        $uniqueSuffix = Str::lower(Str::random(6));
        $thesisName = "thesis_{$slug}_{$timestamp}_{$uniqueSuffix}.pdf";

        $plagiarismScore = null;
        $plagiarismReport = null;
        $plagiarismCheckedAt = null;

        $scan = app(PlagiarismChecker::class)->scan($req->file('thesis_pdf'));

        if ($scan) {
            $plagiarismScore = is_numeric($scan['score'] ?? null)
                ? (int) $scan['score']
                : null;
            $plagiarismReport = $scan['response'] ?? null;
            $plagiarismCheckedAt = now();
        }

        $thesisPath = Storage::disk('spaces')->putFileAs(
            $thesisDir,
            $req->file('thesis_pdf'),
            $thesisName
        );

        Thesis::create([
            'thesis_title_id' => $thesisTitle->id,
            'thesis_pdf_path' => $thesisPath,
            'status' => 'pending',
            'plagiarism_score' => $plagiarismScore,
            'plagiarism_report' => $plagiarismReport,
            'plagiarism_checked_at' => $plagiarismCheckedAt,
        ]);

        $thesisTitle->forceFill([
            'grade' => null,
            'verification_token' => null,
            'panel_chairman' => null,
            'panelist_one' => null,
            'panelist_two' => null,
            'defense_date' => null,
        ])->save();

        return redirect()
            ->route('theses.show', $thesisTitle)
            ->with('status', 'Thesis uploaded. Await adviser review.');
    }

    public function download(Thesis $thesis, string $type)
    {
        Gate::authorize('view', $thesis);

        $thesis->loadMissing('thesisTitle');

        $path = match ($type) {
            'thesis' => $thesis->thesis_pdf_path,
            'endorsement' => optional($thesis->thesisTitle)->endorsement_pdf_path,
            'abstract' => optional($thesis->thesisTitle)->abstract_pdf_path,
            default => null,
        };

        abort_unless($path, 404);

        $temporaryUrl = Storage::disk('spaces')->temporaryUrl($path, now()->addMinutes(5), [
            'ResponseContentType' => 'application/pdf',
        ]);

        return redirect()->away($temporaryUrl);
    }
}
