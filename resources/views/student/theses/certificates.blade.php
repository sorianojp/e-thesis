<x-app-layout>
    <x-slot name="header">
        My Certificates
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded bg-green-50 text-green-900 px-4 py-2">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow sm:rounded p-6">
                <h2 class="text-lg font-semibold text-gray-900">Certificate to Defend</h2>
                <p class="text-sm text-gray-600 mt-1">Download certificates for any stage with fully approved chapters.
                </p>
            </div>

            @forelse ($thesisTitles as $title)
                @php($titleDefenseReady = $title->titleDefenseApproved())
                @php($finalDefenseReady = $title->chaptersAreApproved())
                @php($titleDefenseChapter = $title->theses->first(fn($chap) => in_array($chap->chapter_label, \App\Models\ThesisTitle::titleDefenseChapters(), true) && in_array($chap->status, ['approved', 'passed'], true)))
                @php($finalDefenseChapter = $title->theses->first(fn($chap) => in_array($chap->status, ['approved', 'passed'], true)))
                <div
                    class="bg-white shadow rounded-lg p-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $title->title }}</h3>
                        <p class="text-sm text-gray-600">{{ optional($title->course)->name }}</p>
                        <p class="text-xs text-gray-500 mt-1">Approved chapters: {{ $title->approvedChaptersCount() }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @if ($titleDefenseReady && $titleDefenseChapter)
                            <a href="{{ route('theses.certificate', [$titleDefenseChapter, 'stage' => 'title']) }}"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Title Defense Certificate
                            </a>
                        @else
                            <span class="text-sm text-gray-500">Title Defense certificate unavailable.</span>
                        @endif

                        @if ($finalDefenseReady && $finalDefenseChapter)
                            <a href="{{ route('theses.certificate', [$finalDefenseChapter, 'stage' => 'final']) }}"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Final Defense Certificate
                            </a>
                        @else
                            <span class="text-sm text-gray-500">Final Defense certificate unavailable.</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white shadow sm:rounded p-6 text-sm text-gray-600">
                    No thesis titles yet.
                </div>
            @endforelse

            <div class="bg-white shadow sm:rounded p-6 space-y-3">
                <h2 class="text-lg font-semibold text-gray-900">Approval Sheet</h2>
                <p class="text-sm text-gray-600">The approval sheet becomes available once five chapters (1-5) are
                    approved
                    across your Title Defense and Final Defense.</p>

                @if ($approvalEligible && $approvalSheetThesis)
                    <a href="{{ route('theses.approval', $approvalSheetThesis) }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Approval Sheet
                    </a>
                @else
                    <span class="text-sm text-gray-500">Approval sheet not yet available.</span>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
