<?php

namespace App\Http\Controllers;

use App\Models\Thesis;
use App\Models\ThesisTitle;
use Illuminate\Http\Request;

class VerifyController extends Controller
{
    public function show(string $token, Request $request)
    {
        $thesisTitle = ThesisTitle::with([
            'student:id,name,email',
            'course:id,name',
            'adviserUser:id,name',
            'theses' => fn ($q) => $q->latest('created_at'),
        ])->where('verification_token', $token)->first();

        if (!$thesisTitle) {
            return response()->view('verify.result', ['status' => 'invalid'], 404);
        }

        /** @var Thesis|null $thesis */
        $thesis = $thesisTitle->theses->firstWhere(fn (Thesis $t) => in_array($t->status, ['approved', 'passed'], true));

        if (!$thesis) {
            return response()->view('verify.result', [
                'status' => 'not_approved',
                'thesisTitle' => $thesisTitle,
            ], 200);
        }

        return view('verify.result', [
            'status' => 'valid',
            'thesisTitle' => $thesisTitle,
            'thesis' => $thesis,
        ]);
    }
}
