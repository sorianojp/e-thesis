<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ThesisTitle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ThesisTitleController extends Controller
{
    public function index(Request $req)
    {
        abort_unless($req->user()->isStudent(), 403);

        $studentId = $req->user()->id;

        $thesisTitles = ThesisTitle::query()
            ->with([
                'student:id,name',
                'theses' => fn ($q) => $q->latest('updated_at'),
                'members' => fn ($q) => $q->orderBy('name'),
            ])
            ->withCount('theses')
            ->where(function ($query) use ($studentId) {
                $query
                    ->where('user_id', $studentId)
                    ->orWhereHas('members', fn ($memberQuery) => $memberQuery->where('users.id', $studentId));
            })
            ->latest()
            ->paginate(10);

        return view('student.theses.index', compact('thesisTitles'));
    }

    public function create()
    {
        abort_unless(auth()->user()->isStudent(), 403);

        $courses = Course::all();
        $advisers = User::query()
            ->where('role', User::ROLE_ADVISER)
            ->orderBy('name')
            ->get(['id', 'name']);

        $students = User::query()
            ->where('role', User::ROLE_STUDENT)
            ->where('id', '!=', auth()->id())
            ->orderBy('name')
            ->get(['id', 'name']);

        $previousTitles = ThesisTitle::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->pluck('title')
            ->unique()
            ->values();

        return view('student.theses.create', compact('courses', 'advisers', 'previousTitles', 'students'));
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
            'endorsement_pdf' => [
                'required',
                'file',
                'mimes:pdf',
                'mimetypes:application/pdf,application/x-pdf',
                'max:40960',
            ],
            'members' => ['nullable', 'array', 'max:' . ThesisTitle::MAX_MEMBERS],
            'members.*' => [
                'integer',
                'distinct',
                Rule::notIn([$req->user()->id]),
                Rule::exists('users', 'id')->where('role', User::ROLE_STUDENT),
            ],
        ]);

        $userId = $req->user()->id;
        $prefix = trim((string) env('DO_SPACES_FOLDER', ''), '/');
        $basePath = $prefix ? "{$prefix}/" : '';
        $endorseDir = "{$basePath}endorsements/{$userId}";
        $abstractDir = "{$basePath}abstracts/{$userId}";

        $slug = str($data['title'])->slug()->limit(80, '');
        $timestamp = now()->format('Ymd_His');
        $uniqueSuffix = Str::lower(Str::random(6));

        $endorseName = "endorsement_{$slug}_{$timestamp}_{$uniqueSuffix}.pdf";
        $abstractName = "abstract_{$slug}_{$timestamp}_{$uniqueSuffix}.pdf";

        $endorsementPath = Storage::disk('spaces')->putFileAs(
            $endorseDir,
            $req->file('endorsement_pdf'),
            $endorseName
        );

        $abstractPath = Storage::disk('spaces')->putFileAs(
            $abstractDir,
            $req->file('abstract_pdf'),
            $abstractName
        );

        $thesisTitle = ThesisTitle::create([
            'user_id' => $userId,
            'course_id' => $data['course_id'],
            'adviser_id' => $data['adviser_id'],
            'title' => $data['title'],
            'abstract_pdf_path' => $abstractPath,
            'endorsement_pdf_path' => $endorsementPath,
        ]);

        $memberIds = collect($req->input('members', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->reject(fn ($id) => $id === $userId)
            ->values()
            ->all();

        if ($memberIds !== []) {
            $thesisTitle->members()->sync($memberIds);
        }

        return redirect()
            ->route('theses.show', $thesisTitle)
            ->with('status', 'Title submitted. Upload your thesis document next.');
    }

    public function show(Request $req, ThesisTitle $thesisTitle)
    {
        abort_unless($req->user()->isStudent(), 403);

        $studentId = $req->user()->id;

        abort_unless(
            (int) $thesisTitle->user_id === $studentId || $thesisTitle->hasMember($studentId),
            403
        );

        $thesisTitle->load([
            'student:id,name',
            'theses' => fn ($q) => $q->latest('updated_at'),
            'members' => fn ($q) => $q->orderBy('name'),
        ])->loadCount('theses');

        $requiredChapters = $thesisTitle->requiredChapters();
        $chapters = $thesisTitle->theses->keyBy('chapter_label');
        $isLeader = (int) $thesisTitle->user_id === $studentId;

        return view('student.theses.show', [
            'thesisTitle' => $thesisTitle,
            'requiredChapters' => $requiredChapters,
            'chapters' => $chapters,
            'isLeader' => $isLeader,
        ]);
    }

    public function certificates(Request $req)
    {
        abort_unless($req->user()->isStudent(), 403);

        $studentId = $req->user()->id;
        $thesisTitles = ThesisTitle::query()
            ->where(function ($query) use ($studentId) {
                $query
                    ->where('user_id', $studentId)
                    ->orWhereHas('members', fn ($memberQuery) => $memberQuery->where('users.id', $studentId));
            })
            ->with([
                'course:id,name',
                'student:id,name',
                'members:id,name',
                'theses' => fn ($q) => $q->latest('updated_at'),
            ])
            ->get();

        return view('student.theses.certificates', [
            'thesisTitles' => $thesisTitles,
            'studentId' => $studentId,
        ]);
    }
}
