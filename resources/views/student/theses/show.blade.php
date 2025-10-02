<x-app-layout>
    <x-slot name="header">
        Thesis Title Details
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
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
                        <a class="text-indigo-600 hover:underline"
                            href="{{ route('theses.download', [$firstChapter, 'abstract']) }}">
                            Download Abstract PDF
                        </a>
                    @endif
                    @if ($thesisTitle->endorsement_pdf_path && $firstChapter)
                        <a class="text-indigo-600 hover:underline"
                            href="{{ route('theses.download', [$firstChapter, 'endorsement']) }}">
                            Download Endorsement Letter
                        </a>
                    @endif
                </div>
            </div>

            <div class="bg-white shadow sm:rounded p-6">
                <h3 class="text-lg font-semibold text-gray-900">Chapter Submissions</h3>
                <p class="text-sm text-gray-600 mt-1">Upload the required chapters for this stage. Replacing a
                    manuscript will reset its status to pending for adviser review.</p>

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
                                @if ($chapter && $chapter->thesis_pdf_path)
                                    <div class="mt-3 md:mt-0">
                                        <a class="text-indigo-600 hover:underline text-sm"
                                            href="{{ route('theses.download', [$chapter, 'thesis']) }}">Download current
                                            upload</a>
                                    </div>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('theses.upload', $thesisTitle) }}" class="mt-4"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="chapter_label" value="{{ $chapterLabel }}">
                                @if ($errors->get('chapter_label') && old('chapter_label') === $chapterLabel)
                                    <x-input-error :messages="$errors->get('chapter_label')" class="mb-2" />
                                @endif
                                <div class="flex flex-col md:flex-row md:items-center gap-4">
                                    <div class="flex-1">
                                        <input type="file" name="thesis_pdf" accept="application/pdf,.pdf" required
                                            class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                                        @if ($errors->get('thesis_pdf'))
                                            <x-input-error :messages="$errors->get('thesis_pdf')" class="mt-2" />
                                        @endif
                                    </div>
                                    <x-primary-button type="submit">Upload {{ $chapter ? 'Again' : 'Chapter' }}</x-primary-button>
                                </div>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white shadow sm:rounded p-6">
                <h3 class="text-lg font-semibold text-gray-900">Certificates</h3>
                <p class="text-sm text-gray-600 mt-1">
                    Visit the <a href="{{ route('theses.certificates') }}" class="text-indigo-600 hover:underline">My
                        Certificates</a> page to download your Certificate to Defend and Approval Sheet when they become
                    available.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
