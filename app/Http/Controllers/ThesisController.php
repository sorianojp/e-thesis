<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Thesis;
use App\Models\User;
use App\Services\CopyleaksClient;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ThesisController extends Controller
{
    protected CopyleaksClient $plagiarismClient;

    public function __construct(CopyleaksClient $plagiarismClient)
    {
        $this->plagiarismClient = $plagiarismClient;
    }

    public function index(Request $req)
    {
        abort_unless($req->user()->isStudent(), 403);
        $theses = Thesis::where('user_id', $req->user()->id)->latest()->paginate(10);

        return view('student.theses.index', compact('theses'));
    }

    public function create()
    {
        abort_unless(auth()->user()->isStudent(), 403);
        $courses = Course::all();
        $advisers = User::query()
            ->where('role', User::ROLE_ADVISER)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('student.theses.create', compact('courses', 'advisers'));
    }

    public function store(Request $req)
    {
        abort_unless($req->user()->isStudent(), 403);
        $data = $req->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'title' => ['required', 'string', 'max:255'],
            'adviser_id' => [
                'required',
                Rule::exists('users', 'id')->where('role', User::ROLE_ADVISER),
            ],
            'abstract_pdf' => [
                'required',
                'file',
                'mimes:pdf',
                'mimetypes:application/pdf,application/x-pdf',
                'max:40960',
            ],
            'thesis_pdf' => [
                'required',
                'file',
                'mimes:pdf',
                'mimetypes:application/pdf,application/x-pdf',
                'max:40960',
            ],
            'endorsement_pdf' => [
                'required',
                'file',
                'mimes:pdf',
                'mimetypes:application/pdf,application/x-pdf',
                'max:40960',
            ],
        ]);

        $userId = $req->user()->id;
        $latestVersion = Thesis::where('user_id', $userId)->max('version');
        $version = ($latestVersion ?? 0) + 1;
        $prefix = trim(env('DO_SPACES_FOLDER', ''), '/'); // e.g., "prod" or ""

        $basePath = $prefix ? "{$prefix}/" : '';
        $thesisDir = "{$basePath}theses/{$userId}";
        $endorseDir = "{$basePath}endorsements/{$userId}";
        $abstractDir = "{$basePath}abstracts/{$userId}";

        // build safe filenames
        $slug = str($data['title'])->slug()->limit(80, '');
        $timestamp = now()->format('Ymd_His');

        $thesisName = "thesis_v{$version}_{$slug}_{$timestamp}.pdf";
        $endorseName = "endorsement_v{$version}_{$slug}_{$timestamp}.pdf";
        $abstractName = "abstract_v{$version}_{$slug}_{$timestamp}.pdf";

        $thesisHash = $this->hashUploadedFile($req->file('thesis_pdf'));
        $plagiarismResult = $this->plagiarismClient->submitScan($req->file('thesis_pdf'), [
            'hash' => $thesisHash,
            'title' => $data['title'],
            'user_id' => $userId,
        ]);

        $thesisPath = Storage::disk('spaces')->putFileAs($thesisDir, $req->file('thesis_pdf'), $thesisName);
        $endorsePath = Storage::disk('spaces')->putFileAs($endorseDir, $req->file('endorsement_pdf'), $endorseName);
        $abstractPath = $req->hasFile('abstract_pdf')
            ? Storage::disk('spaces')->putFileAs($abstractDir, $req->file('abstract_pdf'), $abstractName)
            : null;

        $adviserName = User::query()->whereKey($data['adviser_id'])->value('name');

        Thesis::create([
            'user_id' => $userId,
            'course_id' => $data['course_id'],
            'version' => $version,
            'title' => $data['title'],
            'adviser_id' => $data['adviser_id'],
            'adviser' => $adviserName,
            'abstract_pdf_path' => $abstractPath,
            'thesis_pdf_path' => $thesisPath,          // store the key/path from Spaces
            'endorsement_pdf_path' => $endorsePath,
            'plagiarism_score' => $plagiarismResult['score'] ?? null,
            'plagiarism_scan_id' => $plagiarismResult['scan_id'] ?? null,
            'plagiarism_status' => ($plagiarismResult['score'] ?? null) !== null
                ? 'completed'
                : ($plagiarismResult['status'] ?? null),
            'thesis_hash' => $thesisHash,
            // 'status' defaults to pending via migration
        ]);

        return redirect()->route('theses.index')->with('status', 'Submitted. Await admin review.');
    }

    protected function hashUploadedFile(UploadedFile $file): ?string
    {
        try {
            $hash = sha1_file($file->getRealPath());

            return $hash !== false ? $hash : null;
        } catch (\Throwable $e) {
            report($e);
        }

        return null;
    }
    // secure file preview/download (optional hardened)
    public function download(Thesis $thesis, string $type)
    {
        Gate::authorize('view', $thesis);

        $path = match ($type) {
            'thesis' => $thesis->thesis_pdf_path,
            'endorsement' => $thesis->endorsement_pdf_path,
            'abstract' => $thesis->abstract_pdf_path,
            default => null,
        };

        abort_unless($path, 404);

        // 5-minute signed URL
        $temporaryUrl = Storage::disk('spaces')->temporaryUrl($path, now()->addMinutes(5), [
            'ResponseContentType' => 'application/pdf',
            // 'ResponseContentDisposition' => 'inline; filename="thesis.pdf"', // inline view
            // Or force download:
            // 'ResponseContentDisposition' => 'attachment; filename="thesis.pdf"',
        ]);

        return redirect()->away($temporaryUrl);
    }
}
