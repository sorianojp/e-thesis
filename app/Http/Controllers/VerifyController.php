<?php

namespace App\Http\Controllers;
use App\Models\Thesis;
use Illuminate\Http\Request;

class VerifyController extends Controller
{
    public function show(string $token, Request $request)
    {
        $thesis = Thesis::with(['student:id,name,email','course:id,name'])
            ->where('verification_token', $token)
            ->first();

        if (!$thesis) {
            return response()->view('verify.result', ['status' => 'invalid'], 404);
        }

        if ($thesis->status !== 'approved') {
            return response()->view('verify.result', [
                'status' => 'not_approved',
                'thesis' => $thesis,
            ], 200);
        }

        return view('verify.result', [
            'status' => 'valid',
            'thesis' => $thesis,
        ]);
    }
}
