<?php

namespace App\Http\Controllers;

use App\Models\Thesis;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class ThesisController extends Controller
{
    public function index(Request $req) {
        $theses = Thesis::where('user_id', $req->user()->id)->latest()->paginate(10);
        return view('student.theses.index', compact('theses'));
    }

    public function create() {
        $courses = Course::all();
        return view('student.theses.create', compact('courses'));
    }

public function store(Request $req) {
    $data = $req->validate([
        'course_id' => 'required|exists:courses,id',
        'version' => 'required|integer',
        'title' => 'required|string|max:255',
        'adviser' => 'nullable|string|max:255',
        'abstract' => 'nullable|string',
        'thesis_pdf' => 'required|file|mimes:pdf|max:20480',       // 20 MB
        'endorsement_pdf' => 'required|file|mimes:pdf|max:20480',
    ]);

    $userId = $req->user()->id;
    $prefix = trim(env('DO_SPACES_FOLDER', ''), '/'); // e.g., "prod" or ""

    $basePath = $prefix ? "{$prefix}/" : '';
    $thesisDir = "{$basePath}theses/{$userId}";
    $endorseDir = "{$basePath}endorsements/{$userId}";

    // build safe filenames
    $slug = str($data['title'])->slug()->limit(80, '');
    $timestamp = now()->format('Ymd_His');

    $thesisName = "thesis_v{$data['version']}_{$slug}_{$timestamp}.pdf";
    $endorseName = "endorsement_v{$data['version']}_{$slug}_{$timestamp}.pdf";

    $thesisPath = Storage::disk('spaces')->putFileAs($thesisDir, $req->file('thesis_pdf'), $thesisName);
    $endorsePath = Storage::disk('spaces')->putFileAs($endorseDir, $req->file('endorsement_pdf'), $endorseName);

    Thesis::create([
        'user_id' => $userId,
        'course_id' => $data['course_id'],
        'version' => $data['version'],
        'title' => $data['title'],
        'adviser' => $data['adviser'] ?? null,
        'abstract' => $data['abstract'] ?? null,
        'thesis_pdf_path' => $thesisPath,          // store the key/path from Spaces
        'endorsement_pdf_path' => $endorsePath,
        // 'status' defaults to pending via migration
    ]);

    return redirect()->route('theses.index')->with('status', 'Submitted. Await admin review.');
}


    // secure file preview/download (optional hardened)
public function download(Thesis $thesis, string $type) {
    Gate::authorize('view', $thesis);

    $path = $type === 'thesis' ? $thesis->thesis_pdf_path : $thesis->endorsement_pdf_path;

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
