<x-app-layout>
    <x-slot name="header">
        Advisee Titles
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="rounded bg-green-50 text-green-900 px-4 py-2">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow sm:rounded p-6">
                <h2 class="text-lg font-semibold text-gray-900">Assigned Thesis Titles</h2>
                <p class="text-sm text-gray-600 mt-1">Track each advisee’s progress and review submitted chapters.</p>
            </div>

            @forelse ($thesisTitles as $title)
                @php
                    $latest = $title->theses->sortByDesc('updated_at')->first();
                    $requiredCount = count($title->requiredChapters());
                    $approvedCount = $title->theses->whereIn('status', ['approved', 'passed'])->unique('chapter_label')->count();
                    $hasRejected = $title->theses->contains(fn ($chapter) => $chapter->status === 'rejected');
                    $titleDefenseReady = $title->titleDefenseApproved();
                    $finalDefenseReady = $title->chaptersAreApproved();

                    if ($hasRejected) {
                        $statusTuple = ['Rejected', 'bg-red-100 text-red-800'];
                    } elseif ($finalDefenseReady) {
                        $statusTuple = ['Final Defense Ready', 'bg-blue-100 text-blue-800'];
                    } elseif ($titleDefenseReady) {
                        $statusTuple = ['Title Defense Ready', 'bg-green-100 text-green-800'];
                    } elseif ($latest) {
                        $statusTuple = [
                            ucfirst($latest->status),
                            match ($latest->status) {
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'approved' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                'passed' => 'bg-blue-100 text-blue-800',
                                default => 'bg-gray-100 text-gray-700',
                            },
                        ];
                    } else {
                        $statusTuple = ['No submissions yet', 'bg-gray-100 text-gray-700'];
                    }

                    [$latestStatus, $statusClass] = $statusTuple;
                @endphp
                <div class="bg-white shadow rounded-lg p-6 flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $title->title }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ optional($title->course)->name }}</p>
                        <p class="text-sm text-gray-500 mt-2">Student: {{ optional($title->student)->name }}</p>
                        <p class="text-sm text-gray-500 mt-1">Chapters approved: {{ $approvedCount }} / {{ $requiredCount }}</p>
                        <p class="text-sm text-gray-500 mt-1 flex items-center gap-2">
                            <span>Latest status:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium {{ $statusClass }}">
                                {{ $latestStatus }}
                            </span>
                        </p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <a href="{{ route('adviser.theses.show', $title) }}"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Review Chapters
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white shadow rounded-lg p-6 text-sm text-gray-600">
                    No assigned thesis titles yet.
                </div>
            @endforelse

            <div>{{ $thesisTitles->links() }}</div>
        </div>
    </div>
</x-app-layout>
