@php($routePrefix = 'adviser')

<x-app-layout>
    <x-slot name="header">
        Advisee Thesis Title
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded bg-green-50 text-green-900 px-4 py-2">{{ session('status') }}</div>
            @endif

            @php($approvalEligible = $approvalEligible ?? false)
            @php($approvalSheetThesis = $approvalSheetThesis ?? null)

            @php($firstChapter = $chapters->first())
            @php($titleDefenseReady = $thesisTitle->titleDefenseApproved())
            @php($firstApproved = $chapters->firstWhere('status', 'approved') ?? $chapters->firstWhere('status', 'passed'))

            <div class="bg-white shadow sm:rounded p-6">
                <h2 class="text-xl font-semibold text-gray-900">{{ $thesisTitle->title }}</h2>
                <p class="text-sm text-gray-600 mt-1">{{ optional($thesisTitle->course)->name }}</p>
                <p class="text-sm text-gray-500 mt-2">Student: {{ $thesisTitle->student->name }}
                    ({{ $thesisTitle->student->email }})</p>
                <p class="text-sm text-gray-500 mt-1">
                    Adviser: {{ optional($thesisTitle->adviserUser)->name ?? 'Unassigned' }}
                </p>

                <div class="mt-4 flex flex-wrap gap-3 text-sm">
                    @if ($thesisTitle->abstract_pdf_path && $chapters->isNotEmpty())
                        <a class="inline-flex items-center gap-2 text-indigo-600 hover:underline"
                            href="{{ route('theses.download', [$chapters->first(), 'abstract']) }}">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 12 12 16.5 16.5 12" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V3" />
                            </svg>
                            Abstract
                        </a>
                    @endif
                    @if ($thesisTitle->endorsement_pdf_path && $chapters->isNotEmpty())
                        <a class="inline-flex items-center gap-2 text-indigo-600 hover:underline"
                            href="{{ route('theses.download', [$chapters->first(), 'endorsement']) }}">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 12 12 16.5 16.5 12" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V3" />
                            </svg>
                            Endorsement Letter
                        </a>
                    @endif
                </div>
            </div>

            <div class="bg-white shadow sm:rounded p-6">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 inline-flex items-center gap-2">
                            <svg class="h-6 w-6 text-indigo-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4.5 20.25a8.25 8.25 0 1 1 15 0" />
                            </svg>
                            Panel Details
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Panel assignment unlocks once Chapters 1–3 are approved for Title Defense.
                        </p>
                    </div>
                    @if ($titleDefenseReady && $firstApproved)
                        <div>
                            <a href="{{ route('adviser.theses.panel.edit', $firstApproved) }}"
                                class="text-sm text-indigo-600 hover:underline">Edit panel</a>
                        </div>
                    @endif
                </div>

                @if ($titleDefenseReady && $firstApproved)
                    <dl class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-700">
                        <div>
                            <dt class="font-semibold">Chairman</dt>
                            <dd>{{ $thesisTitle->panel_chairman ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold">Panelist 1</dt>
                            <dd>{{ $thesisTitle->panelist_one ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold">Panelist 2</dt>
                            <dd>{{ $thesisTitle->panelist_two ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold">Defense Date</dt>
                            <dd>{{ optional($thesisTitle->defense_date)->format('F d, Y') ?? '—' }}</dd>
                        </div>
                    </dl>
                @else
                    <p class="mt-4 text-sm text-gray-500">Await adviser approval for Chapters 1–3 to enable panel
                        assignment.</p>
                @endif
            </div>

            <div class="bg-white shadow sm:rounded p-6">
                <h3 class="text-lg font-semibold text-gray-900 inline-flex items-center gap-2">
                    <svg class="h-6 w-6 text-indigo-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h6m-6 4h6m1.5 2.25H8.25A2.25 2.25 0 0 1 6 16.5V5.25A2.25 2.25 0 0 1 8.25 3h5.379a2.25 2.25 0 0 1 1.591.659l3.121 3.121a2.25 2.25 0 0 1 .659 1.591V16.5a2.25 2.25 0 0 1-2.25 2.25z" />
                    </svg>
                    Chapter Reviews
                </h3>
                <p class="text-sm text-gray-600 mt-1">Approve or reject each uploaded chapter. Rejections will prompt
                    the
                    student to re-upload and set the status back to pending.</p>

                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                    @foreach ($requiredChapters as $chapterLabel)
                        @php($chapter = $chapters->get($chapterLabel))
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $chapterLabel }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Status:
                                        @php($status = $chapter->status ?? 'not submitted')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium capitalize {{ $status === 'pending'
                                                ? 'bg-yellow-100 text-yellow-800'
                                                : ($status === 'approved'
                                                    ? 'bg-green-100 text-green-800'
                                                    : ($status === 'rejected'
                                                        ? 'bg-red-100 text-red-800'
                                                        : 'bg-gray-100 text-gray-700')) }}">
                                            {{ $status }}
                                        </span>
                                    </p>
                                    @if ($chapter && $chapter->updated_at)
                                        <p class="text-xs text-gray-500 mt-1">Last updated:
                                            {{ $chapter->updated_at->format('F d, Y h:i A') }}</p>
                                    @endif
                                    @if ($chapter)
                                        <div class="mt-3 text-sm text-gray-600">
                                            <p class="font-semibold text-gray-900">Plagiarism Scan</p>
                                            <div class="mt-1">
                                                <x-plagiarism-summary :thesis="$chapter" :compact="true" />
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-3 md:mt-0 space-x-3">
                                    @if ($chapter && $chapter->thesis_pdf_path)
                                        <a class="inline-flex items-center gap-2 text-indigo-600 hover:underline text-sm"
                                            href="{{ route('theses.download', [$chapter, 'thesis']) }}">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="1.5"
                                                aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M7.5 12 12 16.5 16.5 12" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V3" />
                                            </svg>
                                            Manuscript
                                        </a>
                                    @endif
                                </div>
                            </div>

                            @if (!$chapter)
                                <p class="mt-3 text-sm text-gray-500">Waiting for student upload.</p>
                                @continue
                            @endif

                            <div class="mt-4">
                                @if ($chapter->status === 'pending')
                                    <div class="flex flex-wrap gap-2">
                                        <form method="POST"
                                            action="{{ route('adviser.theses.approve', $chapter) }}">
                                            @csrf
                                            <x-primary-button type="submit">
                                                <span class="inline-flex items-center gap-2">
                                                    <svg class="h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="1.5" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M4.5 12.75l6 6 9-13.5" />
                                                    </svg>
                                                    Approve
                                                </span>
                                            </x-primary-button>
                                        </form>
                                        <form method="POST" action="{{ route('adviser.theses.reject', $chapter) }}">
                                            @csrf
                                            <x-danger-button type="submit">
                                                <span class="inline-flex items-center gap-2">
                                                    <svg class="h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="1.5" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6 18 18 6M6 6l12 12" />
                                                    </svg>
                                                    Reject
                                                </span>
                                            </x-danger-button>
                                        </form>
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500">
                                        This chapter is {{ $chapter->status }}.
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
