<?php

namespace App\Http\Controllers\Adviser;

use App\Http\Controllers\Controller;
use App\Models\ThesisTitle;
use Illuminate\Http\Request;

class ThesisTitleReviewController extends Controller
{
    public function index(Request $req)
    {
        abort_unless($req->user()->isAdviser(), 403);

        $thesisTitles = ThesisTitle::query()
            ->with([
                'student:id,name,email',
                'course:id,name',
                'theses' => fn ($q) => $q->latest('updated_at'),
            ])
            ->where('adviser_id', $req->user()->id)
            ->latest()
            ->paginate(12);

        return view('adviser.theses.index', compact('thesisTitles'));
    }

    public function show(Request $req, ThesisTitle $thesisTitle)
    {
        abort_unless($req->user()->isAdviser() && (int) $thesisTitle->adviser_id === $req->user()->id, 403);

        $thesisTitle->load([
            'student:id,name,email',
            'course:id,name',
            'theses' => fn ($q) => $q->latest('updated_at'),
        ]);

        $requiredChapters = $thesisTitle->requiredChapters();
        $chapters = $thesisTitle->theses->keyBy('chapter_label');
        $totalApproved = ThesisTitle::approvedChaptersCountForStudent($thesisTitle->user_id);
        $approvalEligible = $thesisTitle->chaptersAreApproved();
        $approvalSheetThesis = $approvalEligible
            ? $thesisTitle->theses->first(fn ($chapter) => in_array($chapter->status, ['approved', 'passed']))
            : null;

        return view('adviser.theses.show', compact('thesisTitle', 'requiredChapters', 'chapters', 'approvalEligible', 'approvalSheetThesis'));
    }
}
