<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Thesis;
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
        ]);
        return view('admin.theses.show', compact('thesis'));
    }

    public function approve(Request $req, Thesis $thesis) {
        Gate::authorize('admin', Thesis::class);
        Gate::authorize('review', $thesis);
        $data = $req->validate(['adviser_remarks' => 'nullable|string|max:2000']);

        $thesis->loadMissing('thesisTitle');

        $thesis->update([
            'status' => 'approved',
            'adviser_remarks' => $data['adviser_remarks'] ?? null,
            'approved_at' => Carbon::now(),
            'approved_by' => $req->user()->id,
        ]);

        $thesis->thesisTitle->forceFill([
            'grade' => null,
            'verification_token' => $thesis->thesisTitle->verification_token ?: Str::random(48),
        ])->save();

        $prefix = $req->user()->isAdmin() ? 'admin' : 'adviser';

        return redirect()
            ->route($prefix . '.theses.panel.edit', $thesis)
            ->with('status', 'Approved! Please add panel details.');
    }

    public function reject(Request $req, Thesis $thesis) {
        Gate::authorize('admin', Thesis::class);
        Gate::authorize('review', $thesis);
        $data = $req->validate(['adviser_remarks' => 'required|string|max:2000']);

        $thesis->loadMissing('thesisTitle');

        $thesis->update([
            'status' => 'rejected',
            'adviser_remarks' => $data['adviser_remarks'],
            'approved_at' => null,
            'approved_by' => null,
        ]);

        $thesis->thesisTitle->forceFill(['grade' => null])->save();

       $prefix = $req->user()->isAdmin() ? 'admin' : 'adviser';

       return redirect()->route($prefix . '.theses.index')->with('status', 'Rejected!');
    }

    public function editPanel(Thesis $thesis)
    {
        Gate::authorize('admin', Thesis::class);
        Gate::authorize('review', $thesis);
        abort_unless($thesis->status === 'approved', 403);

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

    public function certificate(Thesis $thesis) {
        Gate::authorize('downloadCertificate', $thesis);
        abort_unless(in_array($thesis->status, ['approved', 'passed'], true), 403);

        $thesis->loadMissing([
            'thesisTitle.student:id,name',
            'thesisTitle.course:id,name',
            'thesisTitle.adviserUser:id,name',
        ]);

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

        $pdf = PDF::loadView('pdf.certificate', [
            'thesis'      => $thesis,
            'student'     => $thesis->thesisTitle->student,
            'approvedAt'  => optional($thesis->approved_at)->format('F d, Y'),
            // 'verifyUrl'   => $verifyUrl,
            'qrPng'       => $qrPng,
            'visibleCode' => strtoupper(substr(hash('sha256', $thesis->thesisTitle->verification_token), 0, 10)),
        ])->setPaper('A4');

        $studentSlug = str($thesis->thesisTitle->student->name)->slug('-');
        return $pdf->download("Eligibility_to_Defend_{$studentSlug}.pdf");
    }

    public function approvalSheet(Thesis $thesis)
    {
        Gate::authorize('downloadCertificate', $thesis);
        $thesis->loadMissing([
            'thesisTitle.student:id,name',
            'thesisTitle.course:id,name',
            'thesisTitle.adviserUser:id,name',
        ]);

        abort_unless($thesis->status === 'passed' && !is_null($thesis->thesisTitle->grade), 403);

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

    public function markAsPassed(Request $req, Thesis $thesis)
    {
        Gate::authorize('admin', Thesis::class);
        Gate::authorize('review', $thesis);
        abort_unless($thesis->status === 'approved', 403);

        $data = $req->validate([
            'grade' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $thesis->loadMissing('thesisTitle');

        $thesis->update(['status' => 'passed']);
        $thesis->thesisTitle->update(['grade' => $data['grade']]);

        $prefix = $req->user()->isAdmin() ? 'admin' : 'adviser';

        return redirect()
            ->route($prefix . '.theses.index')
            ->with('status', 'Grade saved. Thesis marked as passed.');
    }

}
