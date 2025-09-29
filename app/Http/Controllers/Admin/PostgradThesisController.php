<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostgradThesis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostgradThesisController extends Controller
{
    public function index()
    {
        $theses = PostgradThesis::query()->latest()->paginate(15);

        return view('admin.postgrad.index', compact('theses'));
    }

    public function create()
    {
        return view('admin.postgrad.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'adviser' => ['required', 'string', 'max:255'],
            'thesis_pdf' => [
                'required',
                'file',
                'mimes:pdf',
                'mimetypes:application/pdf,application/x-pdf',
                'max:40960',
            ],
        ]);

        $userId = $request->user()->id;
        $prefix = trim(env('DO_SPACES_FOLDER', ''), '/');
        $basePath = $prefix ? "{$prefix}/" : '';
        $dir = "{$basePath}postgrad/theses";

        $slug = str($data['title'])->slug('-')->limit(80, '');
        $timestamp = now()->format('Ymd_His');
        $fileName = "postgrad_{$slug}_{$timestamp}.pdf";

        $storedPath = Storage::disk('spaces')->putFileAs($dir, $request->file('thesis_pdf'), $fileName);

        PostgradThesis::create([
            'title' => $data['title'],
            'adviser' => $data['adviser'],
            'thesis_pdf_path' => $storedPath,
            'uploaded_by' => $userId,
        ]);

        return redirect()->route('admin.postgrad.index')->with('status', 'Postgrad thesis uploaded.');
    }

    public function download(PostgradThesis $postgradThesis)
    {
        $temporaryUrl = Storage::disk('spaces')->temporaryUrl(
            $postgradThesis->thesis_pdf_path,
            now()->addMinutes(5),
            ['ResponseContentType' => 'application/pdf']
        );

        return redirect()->away($temporaryUrl);
    }
}
