<x-app-layout>
    <x-slot name="header">
        Thesis Title Details
    </x-slot>

    <div class="py-6 font-sans text-gray-800">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
            <div class="rounded bg-green-50 text-green-900 px-4 py-2">{{ session('status') }}</div>
            @endif

            @if (!$isLeader)
            <div class="rounded bg-blue-50 text-blue-900 px-4 py-2">
                You are viewing this thesis as a team member. Only the leader can upload or replace chapters.
            </div>
            @endif

            <div class="bg-white shadow sm:rounded p-6">
                <h2 class="text-xl font-semibold text-gray-900">{{ $thesisTitle->title }}</h2>
                <p class="text-sm text-gray-600 mt-1">{{ optional($thesisTitle->course)->name }}</p>

                <dl class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                    <div>
                        <dt class="font-semibold">Adviser</dt>
                        <dd>{{ optional($thesisTitle->adviserUser)->name ?? 'Unassigned' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Submissions</dt>
                        <dd>{{ $thesisTitle->theses_count ?? $thesisTitle->theses->count() }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Leader</dt>
                        <dd>{{ optional($thesisTitle->student)->name ?? 'Unassigned' }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="font-semibold">Team Members</dt>
                        <dd>{{ $thesisTitle->members->isNotEmpty() ? $thesisTitle->members->pluck('name')->implode(', ') : '—' }}
                        </dd>
                    </div>
                    @if ($thesisTitle->abstract)
                    <div class="md:col-span-2">
                        <dt class="font-semibold">Abstract</dt>
                        <dd class="mt-1 whitespace-pre-line text-gray-600">{{ $thesisTitle->abstract }}</dd>
                    </div>
                    @endif
                </dl>

                @php($firstChapter = $chapters->first())
                <div class="mt-4 flex flex-wrap gap-3 text-sm">
                    @if ($thesisTitle->abstract_pdf_path && $firstChapter)
                    <a class="inline-flex items-center gap-2 text-indigo-600 hover:underline"
                        href="{{ route('theses.download', [$firstChapter, 'abstract']) }}">
                        <x-icon name="download" class="h-5 w-5" />
                        Abstract PDF
                    </a>
                    @endif
                    @if ($thesisTitle->endorsement_pdf_path && $firstChapter)
                    <a class="inline-flex items-center gap-2 text-indigo-600 hover:underline"
                        href="{{ route('theses.download', [$firstChapter, 'endorsement']) }}">
                        <x-icon name="download" class="h-5 w-5" />
                        Endorsement Letter
                    </a>
                    @endif
                </div>
            </div>

            @php($hasPanel = (bool) ($thesisTitle->panel_chairman || $thesisTitle->panelist_one || $thesisTitle->panelist_two))
            <div class="{{ $hasPanel ? 'bg-green-50' : 'bg-red-50' }} shadow sm:rounded p-6">
                <h3 class="text-lg font-semibold text-gray-900 inline-flex items-center gap-2">
                    <x-icon name="users" class="h-6 w-6 text-indigo-500" />
                    Panel Details
                </h3>
                <p class="text-sm text-gray-600 mt-1">
                    @if ($hasPanel)
                    Your adviser has assigned the panel for this title.
                    @else
                    Panel assignments will appear here once your adviser sets them.
                    @endif
                </p>

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
            </div>

            <div class="bg-white shadow sm:rounded p-6">
                <h3 class="text-lg font-semibold text-gray-900 inline-flex items-center gap-2">
                    <x-icon name="upload" class="h-6 w-6 text-indigo-500" />
                    Chapter Submissions
                </h3>
                <p class="text-sm text-gray-600 mt-1 mb-4">
                    @if ($isLeader)
                    Upload the required chapters for this stage. Replacing a manuscript will reset its status to
                    pending
                    for adviser review.
                    @else
                    Only the leader can upload manuscripts. You can monitor each chapter’s status below.
                    @endif
                </p>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    @foreach ($requiredChapters as $chapterLabel)
                    @php($chapter = $chapters->get($chapterLabel))
                    @php($status = $chapter->status ?? 'not submitted')
                    @php($showUploadForm = $isLeader && in_array($status, ['rejected', 'not submitted'], true))
                    @php($statusClasses = match ($status) {
                    'pending' => 'bg-yellow-50 border-yellow-200',
                    'approved' => 'bg-green-50 border-green-200',
                    'rejected' => 'bg-red-50 border-red-200',
                    default => 'bg-gray-50 border-gray-200',
                    }
                    )
                    <div class="rounded-lg p-4 border {{ $statusClasses }}">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between ">
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
                                    {{ $chapter->updated_at->format('F d, Y h:i A') }}
                                </p>
                                @endif

                                @if ($chapter)
                                <div class="mt-3 text-sm text-gray-600">
                                    <p class="font-semibold text-gray-900">Plagiarism Scan</p>
                                    <div class="mt-1">
                                        <x-plagiarism-summary :thesis="$chapter" :compact="true" />
                                    </div>
                                </div>
                                @endif
                                @if ($chapter && $chapter->thesis_pdf_path)
                                <div class="mt-3 md:mt-0">
                                    <a class="inline-flex items-center gap-2 text-indigo-600 hover:underline text-sm mt-4"
                                        href="{{ route('theses.download', [$chapter, 'thesis']) }}">
                                        <x-icon name="download" class="h-5 w-5" />
                                        Manuscript
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>

                        @if ($showUploadForm)
                        <form method="POST" action="{{ route('theses.upload', $thesisTitle) }}"
                            class="mt-4 flex flex-col gap-3 thesis-upload-form" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="chapter_label" value="{{ $chapterLabel }}">
                            <input type="file" name="thesis_pdf" accept="application/pdf,.pdf"
                                required
                                class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
                            <x-primary-button type="submit"
                                class="w-full flex justify-center hover:bg-transparent bg-[#050565] hover:text-[#050565] hover:border border-[#050565]">
                                <span class="inline-flex items-center gap-2">
                                    <x-icon name="upload" class="items-center h-5 w-5" />
                                    Upload {{ $chapter ? 'Again' : 'Chapter' }}
                                </span>
                            </x-primary-button>
                        </form>

                        @elseif (!$isLeader)
                        <p class="mt-4 text-xs text-gray-500">
                            Only the leader can upload or replace this chapter.
                        </p>
                        @elseif ($status === 'pending')
                        <p class="mt-4 text-xs text-gray-500">This chapter is currently under adviser review.
                            You
                            can upload a new file once feedback has been provided.</p>
                        @else
                        <p class="mt-4 text-xs text-gray-500">This chapter has been approved. Contact your
                            adviser
                            if further changes are required.</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white shadow sm:rounded p-6">
                <h3 class="text-lg font-semibold text-gray-900 inline-flex items-center gap-2">
                    <x-icon name="certificate" class="h-6 w-6 text-indigo-500" />
                    Certificates
                </h3>
                <p class="text-sm text-gray-600 mt-1">
                    Visit the <a href="{{ route('theses.certificates') }}" class="text-indigo-600 hover:underline">My
                        Certificates</a> page to download your Certificate to Defend and Approval Sheet when they become
                    available.
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const uploadForms = document.querySelectorAll('.thesis-upload-form');

            if (!uploadForms.length) {
                return;
            }

            uploadForms.forEach((form) => {
                form.addEventListener('submit', (event) => {
                    const fileInput = form.querySelector('input[type="file"]');

                    if (!fileInput || !fileInput.files.length) {
                        return;
                    }

                    event.preventDefault();

                    if (typeof Swal === 'undefined') {
                        form.submit();
                        return;
                    }

                    Swal.fire({
                        title: 'Uploading thesis...',
                        text: 'Please wait while we upload your file.',
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