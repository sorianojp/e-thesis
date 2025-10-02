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
                        <a class="text-indigo-600 hover:underline"
                            href="{{ route('theses.download', [$chapters->first(), 'abstract']) }}">Download Abstract
                            PDF</a>
                    @endif
                    @if ($thesisTitle->endorsement_pdf_path && $chapters->isNotEmpty())
                        <a class="text-indigo-600 hover:underline"
                            href="{{ route('theses.download', [$chapters->first(), 'endorsement']) }}">Download
                            Endorsement Letter</a>
                    @endif
                </div>
            </div>

            <div class="bg-white shadow sm:rounded p-6">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Panel Details</h3>
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
                <h3 class="text-lg font-semibold text-gray-900">Chapter Reviews</h3>
                <p class="text-sm text-gray-600 mt-1">Approve or reject each uploaded chapter. Rejections will prompt
                    the
                    student to re-upload and set the status back to pending.</p>

                <div class="mt-4 space-y-6">
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
                                        <a class="text-indigo-600 hover:underline text-sm"
                                            href="{{ route('theses.download', [$chapter, 'thesis']) }}">Download
                                            manuscript</a>
                                    @endif
                                </div>
                            </div>

                            @if (!$chapter)
                                <p class="mt-3 text-sm text-gray-500">Waiting for student upload.</p>
                                @continue
                            @endif

                            <div class="mt-4 flex flex-wrap gap-2">
                                <form method="POST" action="{{ route('adviser.theses.approve', $chapter) }}">
                                    @csrf
                                    <x-primary-button type="submit">Approve</x-primary-button>
                                </form>
                                <form method="POST" action="{{ route('adviser.theses.reject', $chapter) }}">
                                    @csrf
                                    <x-danger-button type="submit">Reject</x-danger-button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @php($certificateReady = $thesisTitle->chaptersAreApproved())

            <div class="bg-white shadow sm:rounded p-6">
                <h3 class="text-lg font-semibold text-gray-900">Certificates & Documents</h3>
                <p class="text-sm text-gray-600 mt-1">
                    Certificates are shown on the student's <strong>My Certificates</strong> page once available.
                </p>
                <div class="mt-3 flex flex-wrap gap-2 text-sm text-gray-600">
                    <span>
                        Title Defense: {{ $titleDefenseReady ? 'Ready' : 'Pending' }} • Final Defense:
                        {{ $certificateReady ? 'Ready' : 'Pending' }}
                    </span>

                    @if ($approvalEligible && $approvalSheetThesis)
                        <a class="text-indigo-600 hover:underline text-sm inline-flex items-center"
                            href="{{ route('theses.approval', $approvalSheetThesis) }}">Download Approval Sheet</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
