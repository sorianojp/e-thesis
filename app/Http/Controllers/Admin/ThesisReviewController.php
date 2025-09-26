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
            ->with(['student:id,name,email', 'course:id,name', 'adviserUser:id,name'])
            ->latest();

        if ($req->user()->isAdviser() && !$req->user()->isAdmin()) {
            $q->where('adviser_id', $req->user()->id);
        }
        if ($s = $req->get('status')) $q->where('status', $s);
        $theses = $q->paginate(15);
        return view('admin.theses.index', compact('theses'));
    }

    public function show(Thesis $thesis) {
        Gate::authorize('admin', Thesis::class);
        Gate::authorize('view', $thesis);
        $thesis->loadMissing(['student:id,name,email', 'course:id,name', 'adviserUser:id,name']);
        return view('admin.theses.show', compact('thesis'));
    }

    public function approve(Request $req, Thesis $thesis) {
        Gate::authorize('admin', Thesis::class);
        Gate::authorize('review', $thesis);
        $data = $req->validate(['admin_remarks' => 'nullable|string|max:2000']);

        $thesis->update([
          'status' => 'approved',
          'admin_remarks' => $data['admin_remarks'] ?? null,
          'approved_at' => Carbon::now(),
          'approved_by' => $req->user()->id,
          'verification_token' => $thesis->verification_token ?: Str::random(48),
        ]);

        $prefix = $req->user()->isAdmin() ? 'admin' : 'adviser';

        return redirect()
            ->route($prefix . '.theses.panel.edit', $thesis)
            ->with('status', 'Approved! Please add panel details.');
    }

    public function reject(Request $req, Thesis $thesis) {
        Gate::authorize('admin', Thesis::class);
        Gate::authorize('review', $thesis);
        $data = $req->validate(['admin_remarks' => 'required|string|max:2000']);

        $thesis->update([
          'status' => 'rejected',
          'admin_remarks' => $data['admin_remarks'],
          'approved_at' => null,
          'approved_by' => null,
        ]);

       $prefix = $req->user()->isAdmin() ? 'admin' : 'adviser';

       return redirect()->route($prefix . '.theses.index')->with('status', 'Rejected!');
    }

    public function editPanel(Thesis $thesis)
    {
        Gate::authorize('admin', Thesis::class);
        Gate::authorize('review', $thesis);
        abort_unless($thesis->status === 'approved', 403);

        $thesis->loadMissing(['student:id,name', 'course:id,name']);

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

        $thesis->update($data);

        $prefix = $req->user()->isAdmin() ? 'admin' : 'adviser';

        return redirect()
            ->route($prefix . '.theses.index')
            ->with('status', 'Panel details saved.');
    }

    public function certificate(Thesis $thesis) {
        Gate::authorize('downloadCertificate', $thesis);
        abort_unless($thesis->status === 'approved', 403);

        // Ensure token exists (in case this was approved before you added tokens)
        if (!$thesis->verification_token) {
            $thesis->forceFill(['verification_token' => Str::random(48)])->save();
        }

        $verifyUrl = route('verify.show', ['token' => $thesis->verification_token]);

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
            'student'     => $thesis->student,
            'approvedAt'  => optional($thesis->approved_at)->format('F d, Y'),
            // 'verifyUrl'   => $verifyUrl,
            'qrPng'       => $qrPng,
            'visibleCode' => strtoupper(substr(hash('sha256', $thesis->verification_token), 0, 10)),
        ])->setPaper('A4');

        $studentSlug = str($thesis->student->name)->slug('-');
        return $pdf->download("Eligibility_to_Defend_{$studentSlug}.pdf");
    }

}
