@php($routePrefix = 'adviser')

<x-app-layout>
    <x-slot name="header">
        Advisee Thesis Title
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded bg-green-50 text-green-900 px-4 py-2">{{ session('status') }}</div>
            @endif

            @php($approvalEligible = $approvalEligible ?? false)
            @php($approvalSheetThesis = $approvalSheetThesis ?? null)

            @php($firstChapter = $chapters->first())
            @php($titleDefenseReady = $thesisTitle->titleDefenseApproved())
            @php($firstApproved = $chapters->firstWhere('status', 'approved'))
            @php($hasPanel = (bool) ($thesisTitle->panel_chairman || $thesisTitle->panelist_one || $thesisTitle->panelist_two))

            <div class="bg-white shadow sm:rounded p-6">
                <h2 class="text-xl font-semibold text-gray-900">{{ $thesisTitle->title }}</h2>
                <p class="text-sm text-gray-600 mt-1">{{ optional($thesisTitle->course)->name }}</p>
                <p class="text-sm text-gray-500 mt-2">Leader: {{ optional($thesisTitle->student)->name ?? 'Unassigned' }}
                    @if ($thesisTitle->student && $thesisTitle->student->email)
                        ({{ $thesisTitle->student->email }})
                    @endif
                </p>
                <p class="text-sm text-gray-500 mt-1">
                    Members:
                    {{ $thesisTitle->members->isNotEmpty() ? $thesisTitle->members->pluck('name')->implode(', ') : 'None' }}
                </p>
                <p class="text-sm text-gray-500 mt-1">
                    Adviser: {{ optional($thesisTitle->adviserUser)->name ?? 'Unassigned' }}
                </p>

                <div class="mt-4 flex flex-wrap gap-3 text-sm">
                    @if ($thesisTitle->abstract_pdf_path && $chapters->isNotEmpty())
                        <a class="inline-flex items-center gap-2 text-indigo-600 hover:underline"
                            href="{{ route('theses.download', [$chapters->first(), 'abstract']) }}">
                            <x-icon name="download" class="h-5 w-5" />
                            Abstract
                        </a>
                    @endif
                    @if ($thesisTitle->endorsement_pdf_path && $chapters->isNotEmpty())
                        <a class="inline-flex items-center gap-2 text-indigo-600 hover:underline"
                            href="{{ route('theses.download', [$chapters->first(), 'endorsement']) }}">
                            <x-icon name="download" class="h-5 w-5" />
                            Endorsement Letter
                        </a>
                    @endif
                </div>
            </div>

            <div class="{{ $hasPanel ? 'bg-green-50' : 'bg-red-50' }} shadow sm:rounded p-6">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 inline-flex items-center gap-2">
                            <x-icon name="users" class="h-6 w-6 text-indigo-500" />
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
                    <x-icon name="document-text" class="h-6 w-6 text-indigo-500" />
                    Chapter Reviews
                </h3>
                <p class="text-sm text-gray-600 mt-1">Approve or reject each uploaded chapter. Rejections will prompt
                    the
                    student to re-upload and set the status back to pending.</p>

                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                    @foreach ($requiredChapters as $chapterLabel)
                        @php($chapter = $chapters->get($chapterLabel))
                        @php($status = $chapter->status ?? 'not submitted')
                        @php(
    $statusClasses = match ($status) {
        'pending' => 'bg-yellow-50 border-yellow-200',
        'approved' => 'bg-green-50 border-green-200',
        'rejected' => 'bg-red-50 border-red-200',
        default => 'bg-gray-50 border-gray-200',
    },
)
                        <div class="rounded-lg p-4 border {{ $statusClasses }}">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $chapterLabel }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Status:
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
                                            <x-icon name="download" class="h-5 w-5" />
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
                                        <form method="POST" action="{{ route('adviser.theses.approve', $chapter) }}"
                                            class="adviser-review-form" data-review-action="approve">
                                            @csrf
                                            <x-primary-button type="submit">
                                                <span class="inline-flex items-center gap-2">
                                                    <x-icon name="check" class="h-4 w-4 text-white" />
                                                    Approve
                                                </span>
                                            </x-primary-button>
                                        </form>
                                        <form method="POST" action="{{ route('adviser.theses.reject', $chapter) }}"
                                            class="adviser-review-form" data-review-action="reject">
                                            @csrf
                                            <x-danger-button type="submit">
                                                <span class="inline-flex items-center gap-2">
                                                    <x-icon name="x-mark" class="h-4 w-4 text-white" />
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const reviewForms = document.querySelectorAll('.adviser-review-form');

            if (!reviewForms.length) {
                return;
            }

            reviewForms.forEach((form) => {
                form.addEventListener('submit', (event) => {
                    event.preventDefault();

                    if (typeof Swal === 'undefined') {
                        form.submit();
                        return;
                    }

                    const action = form.dataset.reviewAction || 'submit';
                    const actionLabel = action === 'approve' ? 'approval' : action === 'reject' ?
                        'rejection' : 'decision';

                    Swal.fire({
                        title: `Submitting ${actionLabel}...`,
                        text: 'Please wait while we process this update.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            form.submit();
                        },
                    });
                });
            });
        });
    </script>
</x-app-layout>
