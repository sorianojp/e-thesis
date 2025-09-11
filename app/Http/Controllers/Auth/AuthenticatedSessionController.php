<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */

    public function store(Request $request): RedirectResponse
    {
        // Validate inputs
        $validated = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required','string','min:6'],
            'remember' => ['nullable','boolean'],
        ]);

        try {
            // Call STEP API
            $response = Http::timeout(10)
                ->acceptJson()
                ->asJson()
                ->post(config('services.step.base_url').'/api/other/login', [
                    'email'    => $validated['email'],
                    'password' => $validated['password'],
                ]);
        } catch (\Throwable $e) {
            // Network/DNS/timeout, etc.
            throw ValidationException::withMessages([
                'email' => 'Unable to reach STEP at the moment. Please try again.',
            ]);
        }

        if (!$response->ok()) {
            // Non-200 or validation/auth failure from STEP
            throw ValidationException::withMessages([
                'email' => 'Invalid STEP credentials.',
            ]);
        }

        $step = $response->json();

        // (Adjust these keys to match the exact STEP payload)
        $stepUser  = data_get($step, 'user', []);
        $stepEmail = data_get($stepUser, 'email');
        $stepName  = data_get($stepUser, 'full_name') ?: data_get($stepUser, 'name');
        $stepToken = data_get($step, 'token');

        if (!$stepEmail || !$stepToken) {
            // Defensive: malformed response
            throw ValidationException::withMessages([
                'email' => 'Login failed due to an unexpected response from STEP.',
            ]);
        }

        // Create/update local user
        $user = User::updateOrCreate(
            ['email' => $stepEmail],
            [
                'name'       => $stepName ?: $stepEmail,
                // Never store plaintext; use a random hashed password since STEP is the source of truth
                'password'   => Hash::make(Str::password(32)),
                'step_token' => $stepToken, // Stored encrypted (see casts in User model below)
            ]
        );

        // Optionally stash token in session too (short-lived use)
        session(['step_token' => $stepToken]);

        // Local login (honor "remember me")
        $remember = (bool) ($validated['remember'] ?? false);
        Auth::login($user, $remember);

        // Regenerate session to prevent fixation
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    // public function store(LoginRequest $request): RedirectResponse
    // {
    //     $request->authenticate();

    //     $request->session()->regenerate();

    //     return redirect()->intended(route('dashboard', absolute: false));
    // }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
