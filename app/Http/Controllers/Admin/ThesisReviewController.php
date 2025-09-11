<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Thesis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use PDF;

class ThesisReviewController extends Controller
{
    public function index(Request $req) {
        Gate::authorize('admin', Thesis::class);
        $q = Thesis::query()->with('student:id,name,email')->latest();
        if ($s = $req->get('status')) $q->where('status', $s);
        $theses = $q->paginate(15);
        return view('admin.theses.index', compact('theses'));
    }

    public function show(Thesis $thesis) {
        Gate::authorize('admin', Thesis::class);
        return view('admin.theses.show', compact('thesis'));
    }

    public function approve(Request $req, Thesis $thesis) {
        Gate::authorize('admin', Thesis::class);
        $data = $req->validate(['admin_remarks' => 'nullable|string|max:2000']);

        $thesis->update([
          'status' => 'approved',
          'admin_remarks' => $data['admin_remarks'] ?? null,
          'approved_at' => Carbon::now(),
          'approved_by' => $req->user()->id,
        ]);

        return redirect()->route('admin.theses.index')->with('status', 'Approved!');
    }

    public function reject(Request $req, Thesis $thesis) {
        Gate::authorize('admin', Thesis::class);
        $data = $req->validate(['admin_remarks' => 'required|string|max:2000']);

        $thesis->update([
          'status' => 'rejected',
          'admin_remarks' => $data['admin_remarks'],
          'approved_at' => null,
          'approved_by' => null,
        ]);

       return redirect()->route('admin.theses.index')->with('status', 'Rejected!');
    }

    public function certificate(Thesis $thesis) {
        Gate::authorize('downloadCertificate', $thesis);
        abort_unless($thesis->status === 'approved', 403);

        $pdf = PDF::loadView('pdf.certificate', [
          'thesis' => $thesis,
          'student' => $thesis->student,
          'approvedAt' => $thesis->approved_at->format('F d, Y'),
          // 'controller' => $thesis->approver?->name ?? 'Thesis Coordinator',
        ])->setPaper('A4');

        return $pdf->download("Eligibility_to_Defend_{$thesis->student->name}.pdf");
    }
}
