<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Thesis;
use App\Models\ThesisTitle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ThesisReviewController extends Controller
{
    public function index(Request $req) {
        Gate::authorize('admin', Thesis::class);
        $q = Thesis::query()
            ->with([
                'thesisTitle.student:id,name,email',
                'thesisTitle.course:id,name',
                'thesisTitle.adviserUser:id,name',
            ])
            ->latest();

        if ($req->user()->isAdviser() && !$req->user()->isAdmin()) {
            $q->whereHas('thesisTitle', fn ($sub) => $sub->where('adviser_id', $req->user()->id));
        }
        if ($s = $req->get('status')) $q->where('status', $s);
        $theses = $q->paginate(15);
        return view('admin.theses.index', compact('theses'));
    }

    public function show(Thesis $thesis) {
        Gate::authorize('admin', Thesis::class);
        Gate::authorize('view', $thesis);
        $thesis->loadMissing([
            'thesisTitle.student:id,name,email',
            'thesisTitle.course:id,name',
            'thesisTitle.adviserUser:id,name',
            'thesisTitle.theses' => fn ($q) => $q->latest('updated_at'),
        ]);
        $approvalEligible = $thesis->thesisTitle->chaptersAreApproved();
        $approvalSheetThesis = $approvalEligible
            ? $thesis->thesisTitle->theses->first(fn ($chapter) => $chapter->status === 'approved')
            : null;

        return view('admin.theses.show', compact('thesis', 'approvalEligible', 'approvalSheetThesis'));
    }

    public function approve(Request $req, Thesis $thesis) {
        Gate::authorize('admin', Thesis::class);
        Gate::authorize('review', $thesis);
        $req->validate([]);

        $thesis->loadMissing('thesisTitle');

        $thesis->update([
            'status' => 'approved',
            'approved_at' => Carbon::now(),
            'approved_by' => $req->user()->id,
        ]);

        if ($thesis->thesisTitle->chaptersAreApproved() && !$thesis->thesisTitle->verification_token) {
            $thesis->thesisTitle->forceFill(['verification_token' => Str::random(48)])->save();
        }

        return back()->with('status', "{$thesis->chapter_label} approved.");
    }

    public function reject(Request $req, Thesis $thesis) {
        Gate::authorize('admin', Thesis::class);
        Gate::authorize('review', $thesis);
        $req->validate([]);

        $thesis->loadMissing('thesisTitle');

        $thesis->update([
            'status' => 'rejected',
            'approved_at' => null,
            'approved_by' => null,
        ]);

        return back()->with('status', "{$thesis->chapter_label} rejected.");
    }

    public function editPanel(Thesis $thesis)
    {
        Gate::authorize('admin', Thesis::class);
        Gate::authorize('review', $thesis);
        abort_unless($thesis->status === 'approved', 403);
        abort_unless($thesis->thesisTitle->titleDefenseApproved(), 403);

        $thesis->loadMissing([
            'thesisTitle.student:id,name',
            'thesisTitle.course:id,name',
        ]);

        return view('admin.theses.panel', compact('thesis'));
    }

    public function updatePanel(Request $req, Thesis $thesis)
    {
        Gate::authorize('admin', Thesis::class);
        Gate::authorize('review', $thesis);
        abort_unless($thesis->status === 'approved', 403);
        abort_unless($thesis->thesisTitle->titleDefenseApproved(), 403);

        $data = $req->validate([
            'panel_chairman' => ['required', 'string', 'max:255'],
            'panelist_one' => ['required', 'string', 'max:255'],
            'panelist_two' => ['required', 'string', 'max:255'],
            'defense_date' => ['required', 'date'],
        ]);

        $thesis->thesisTitle->update($data);

        $prefix = $req->user()->isAdmin() ? 'admin' : 'adviser';

        return redirect()
            ->route($prefix . '.theses.index')
            ->with('status', 'Panel details saved.');
    }

    public function certificate(Thesis $thesis)
    {
        Gate::authorize('downloadCertificate', $thesis);
        abort_unless($thesis->status === 'approved', 403);

        $stage = request()->query('stage', 'final');
        abort_unless(in_array($stage, ['title', 'final'], true), 404);

        $thesis->loadMissing([
            'thesisTitle.student:id,name',
            'thesisTitle.course:id,name',
            'thesisTitle.adviserUser:id,name',
            'thesisTitle.members:id,name',
            'thesisTitle.theses' => fn ($q) => $q->latest('updated_at'),
        ]);

        if ($stage === 'title') {
            abort_unless($thesis->thesisTitle->titleDefenseApproved(), 403);
        } else {
            abort_unless($thesis->thesisTitle->chaptersAreApproved(), 403);
        }

        if (!$thesis->thesisTitle->verification_token) {
            $thesis->thesisTitle->forceFill(['verification_token' => Str::random(48)])->save();
        }

        $verifyUrl = route('verify.show', ['token' => $thesis->thesisTitle->verification_token]);

        // ✅ Generate a DOMPDF-friendly PNG QR and base64 it
        $qrPng = base64_encode(
            QrCode::format('png')
                ->size(220)           // pixels
                ->margin(0)
                ->errorCorrection('M')
                ->generate($verifyUrl)
        );

        $stageName = $stage === 'title' ? 'Title Defense' : 'Final Defense';
        $stageLabel = "Certificate of Eligibility to Defend - {$stageName}";

        $pdf = PDF::loadView('pdf.certificate', [
            'thesis'      => $thesis,
            'student'     => $thesis->thesisTitle->student,
            'approvedAt'  => optional($thesis->approved_at)->format('F d, Y'),
            // 'verifyUrl'   => $verifyUrl,
            'qrPng'       => $qrPng,
            'visibleCode' => strtoupper(substr(hash('sha256', $thesis->thesisTitle->verification_token), 0, 10)),
            'stageLabel'  => $stageLabel,
            'stageName'   => $stageName,
        ])->setPaper('A4');

        $studentSlug = str($thesis->thesisTitle->student->name)->slug('-');
        $fileSuffix = $stage === 'title' ? 'Title_Defense' : 'Final_Defense';

        return $pdf->download("Eligibility_to_Defend_{$fileSuffix}_{$studentSlug}.pdf");
    }

    public function approvalSheet(Thesis $thesis)
    {
        Gate::authorize('downloadCertificate', $thesis);
        $thesis->loadMissing([
            'thesisTitle.student:id,name',
            'thesisTitle.course:id,name',
            'thesisTitle.adviserUser:id,name',
            'thesisTitle.members:id,name',
        ]);

        abort_unless($thesis->thesisTitle->chaptersAreApproved(), 403);

        $pdf = PDF::loadView('pdf.approval_sheet', [
            'thesis'      => $thesis,
            'student'     => $thesis->thesisTitle->student,
            'courseName'  => optional($thesis->thesisTitle->course)->name,
            'adviserName' => optional($thesis->thesisTitle->adviserUser)->name,
            'defenseDate' => optional($thesis->thesisTitle->defense_date)->format('F d, Y'),
        ])->setPaper('A4');

        $studentSlug = str($thesis->thesisTitle->student->name)->slug('-');

        return $pdf->download("Approval_Sheet_{$studentSlug}.pdf");
    }
}
