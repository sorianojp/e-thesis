<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Thesis Verification • {{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-900">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-2xl">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-xl font-semibold tracking-tight">Thesis Verification</h1>
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8">
            </div>
            <div class="bg-white rounded-2xl shadow p-6 ring-1 ring-gray-200">
                @if ($status === 'valid')
                    @php
                        $verifyUrl = route('verify.show', ['token' => $thesis->verification_token]);
                        $visibleCode = strtoupper(substr(hash('sha256', $thesis->verification_token), 0, 10));
                    @endphp
                    <div class="flex items-start gap-3 rounded-lg border border-green-200 bg-green-50 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 mt-0.5"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293A1 1 0 106.293 10.707l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <div>
                            <p class="font-semibold text-green-800">Certificate is VALID</p>
                            <p class="text-sm text-green-700">This record matches an approved submission in our system.
                            </p>
                        </div>
                    </div>
                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-1">
                            <p class="text-xs uppercase tracking-wider text-gray-500">Verification Code</p>
                            <p class="mt-1 font-mono text-sm">{{ $visibleCode }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-xs uppercase tracking-wider text-gray-500">Verification Link</p>
                            <p class="mt-1 break-all text-sm text-blue-700 underline"><a
                                    href="{{ $verifyUrl }}">{{ $verifyUrl }}</a></p>
                        </div>
                    </div>
                    <dl class="mt-6 divide-y divide-gray-100 border rounded-xl">
                        <div class="grid grid-cols-3 gap-4 p-4">
                            <dt class="text-gray-500">Student</dt>
                            <dd class="col-span-2 text-gray-900">{{ $thesis->student->name }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-4 p-4">
                            <dt class="text-gray-500">Course</dt>
                            <dd class="col-span-2 text-gray-900">{{ $thesis->course->name }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-4 p-4">
                            <dt class="text-gray-500">Title</dt>
                            <dd class="col-span-2 text-gray-900">{{ $thesis->title }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-4 p-4">
                            <dt class="text-gray-500">Approved on</dt>
                            <dd class="col-span-2 text-gray-900">{{ optional($thesis->approved_at)->format('F d, Y') }}
                            </dd>
                        </div>
                    </dl>
                    <div class="mt-6 flex flex-wrap items-center gap-2">
                        <button type="button" data-copy="{{ $verifyUrl }}"
                            class="copy-btn inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                            </svg>
                            Copy link
                        </button>
                        <button type="button" data-copy="{{ $visibleCode }}"
                            class="copy-btn inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor">
                                <path d="M16 12H8m0 0l4-4m-4 4l4 4" />
                                <rect x="3" y="3" width="18" height="18" rx="2" />
                            </svg>
                            Copy code
                        </button>
                        <button type="button" onclick="window.print()"
                            class="inline-flex items-center gap-1 rounded-lg bg-gray-900 text-white px-3 py-2 text-sm hover:bg-black">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                fill="currentColor">
                                <path d="M6 9V2h12v7H6z" />
                                <path d="M6 18h12v4H6z" />
                                <path d="M4 12h16v4H4z" />
                            </svg>
                            Print
                        </button>
                    </div>

                    <p class="mt-4 text-xs text-gray-500">Verification timestamp: {{ now()->format('F d, Y h:i A') }}
                        (UTC{{ now()->format('P') }})</p>
                @elseif ($status === 'not_approved')
                    @php
                        $verifyUrl = route('verify.show', ['token' => $thesis->verification_token]);
                        $visibleCode = strtoupper(substr(hash('sha256', $thesis->verification_token), 0, 10));
                    @endphp

                    <div class="flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600 mt-0.5"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 6h2v5H9V6zm0 6h2v2H9v-2z"
                                clip-rule="evenodd" />
                        </svg>
                        <div>
                            <p class="font-semibold text-amber-800">Record found, but NOT APPROVED</p>
                            <p class="text-sm text-amber-700">This thesis exists but hasn’t been marked as approved.</p>
                        </div>
                    </div>

                    <dl class="mt-6 divide-y divide-gray-100 border rounded-xl">
                        <div class="grid grid-cols-3 gap-4 p-4">
                            <dt class="text-gray-500">Student</dt>
                            <dd class="col-span-2 text-gray-900">{{ $thesis->student->name }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-4 p-4">
                            <dt class="text-gray-500">Course</dt>
                            <dd class="col-span-2 text-gray-900">{{ $thesis->course->name }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-4 p-4">
                            <dt class="text-gray-500">Title</dt>
                            <dd class="col-span-2 text-gray-900">{{ $thesis->title }}</dd>
                        </div>
                    </dl>

                    <div class="mt-6 flex flex-wrap items-center gap-2">
                        <button type="button" data-copy="{{ $verifyUrl }}"
                            class="copy-btn inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2">
                                </rect>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                            </svg>
                            Copy link
                        </button>
                        <button type="button" data-copy="{{ $visibleCode }}"
                            class="copy-btn inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor">
                                <path d="M16 12H8m0 0l4-4m-4 4l4 4" />
                                <rect x="3" y="3" width="18" height="18" rx="2" />
                            </svg>
                            Copy code
                        </button>
                    </div>
                @else
                    <div class="flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 mt-0.5"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM7.293 7.293a1 1 0 011.414 0L10 8.586l1.293-1.293a1 1 0 111.414 1.414L11.414 10l1.293 1.293a1 1 0 01-1.414 1.414L10 11.414l-1.293 1.293a1 1 0 01-1.414-1.414L8.586 10 7.293 8.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                        <div>
                            <p class="font-semibold text-red-800">Invalid or unknown verification code</p>
                            <p class="text-sm text-red-700">The code may be incorrect, expired, or the certificate has
                                been revoked.</p>
                        </div>
                    </div>

                    <p class="mt-6 text-sm text-gray-700">
                        If you think this is an error, please contact the program office with the verification link you
                        scanned.
                    </p>
                @endif
            </div>

            <p class="mt-4 text-xs text-gray-500 text-center">
                &copy; {{ date('Y') }} — {{ config('app.name', 'Laravel') }} • Served
                {{ now()->format('F d, Y h:i A') }}
            </p>
        </div>
    </div>

    <script>
        document.querySelectorAll('.copy-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const text = btn.getAttribute('data-copy');
                try {
                    await navigator.clipboard.writeText(text);
                    btn.classList.add('ring-2', 'ring-green-300');
                    btn.textContent = 'Copied!';
                    setTimeout(() => {
                        btn.classList.remove('ring-2', 'ring-green-300');
                        btn.textContent = btn.textContent.includes('code') ? 'Copy code' :
                            'Copy link';
                        location.reload();
                    }, 900);
                } catch {}
            });
        });
    </script>
</body>

</html>
