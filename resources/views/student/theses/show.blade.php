<x-app-layout>
    <x-slot name="header">
        Thesis Title Details
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded bg-green-50 text-green-900 px-4 py-2">{{ session('status') }}</div>
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

            <div class="bg-white shadow sm:rounded p-6">
                <h3 class="text-lg font-semibold text-gray-900 inline-flex items-center gap-2">
                    <x-icon name="upload" class="h-6 w-6 text-indigo-500" />
                    Chapter Submissions
                </h3>
                <p class="text-sm text-gray-600 mt-1">Upload the required chapters for this stage. Replacing a
                    manuscript will reset its status to pending for adviser review.</p>

                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                    @foreach ($requiredChapters as $chapterLabel)
                        @php($chapter = $chapters->get($chapterLabel))
                        @php($status = $chapter->status ?? 'not submitted')
                        @php($showUploadForm = in_array($status, ['rejected', 'not submitted'], true))
                        <div class="border border-gray-200 rounded-lg p-4">
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
                                @if ($chapter && $chapter->thesis_pdf_path)
                                    <div class="mt-3 md:mt-0">
                                        <a class="inline-flex items-center gap-2 text-indigo-600 hover:underline text-sm"
                                            href="{{ route('theses.download', [$chapter, 'thesis']) }}">
                                            <x-icon name="download" class="h-5 w-5" />
                                            Manuscript
                                        </a>
                                    </div>
                                @endif
                            </div>

                            @if ($showUploadForm)
                                <form method="POST" action="{{ route('theses.upload', $thesisTitle) }}" class="mt-4"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="chapter_label" value="{{ $chapterLabel }}">
                                    @if ($errors->get('chapter_label') && old('chapter_label') === $chapterLabel)
                                        <x-input-error :messages="$errors->get('chapter_label')" class="mb-2" />
                                    @endif
                                    <div class="flex flex-col md:flex-row md:items-center gap-4">
                                        <div class="flex-1">
                                            <input type="file" name="thesis_pdf" accept="application/pdf,.pdf"
                                                required
                                                class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                                            @if ($errors->get('thesis_pdf'))
                                                <x-input-error :messages="$errors->get('thesis_pdf')" class="mt-2" />
                                            @endif
                                        </div>
                                        <x-primary-button type="submit">
                                            <span class="inline-flex items-center gap-2">
                                                <x-icon name="upload" class="h-4 w-4 text-white" />
                                                Upload {{ $chapter ? 'Again' : 'Chapter' }}
                                            </span>
                                        </x-primary-button>
                                    </div>
                                </form>
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
</x-app-layout>
