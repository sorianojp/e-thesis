<x-app-layout>
    <x-slot name="header">
        My Submissions
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded bg-green-50 text-green-900 px-4 py-2">
                    {{ session('status') }}
                </div>
            @endif
            <div class="flex items-center justify-end">
                <a href="{{ route('theses.create') }}">
                    <x-primary-button type="button" class="gap-2">
                        <x-icon name="plus" class="h-4 w-4" />
                        Title
                    </x-primary-button>
                </a>
            </div>

            @forelse ($thesisTitles as $title)
                @php
                    $latestChapters = $title->theses
                        ->sortByDesc('updated_at')
                        ->unique('chapter_label')
                        ->sortBy('chapter_label')
                        ->values();

                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'approved' => 'bg-green-100 text-green-800',
                        'rejected' => 'bg-red-100 text-red-800',
                    ];

                    $isLeader = (int) $title->user_id === auth()->id();
                    $teamMembers = $title->members
                        ->map(fn($member) => (int) $member->id === auth()->id()
                            ? "{$member->name} (You)"
                            : $member->name);
                    $leaderName = optional($title->student)->name;

                    $titleDefenseReady = $title->titleDefenseApproved();
                    $finalDefenseReady = $title->chaptersAreApproved();
                @endphp

                <div class="bg-white shadow rounded-lg p-6 space-y-6">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-b border-gray-200 pb-4">
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Title</p>
                            <h3 class="text-lg font-semibold text-gray-900 mt-1">{{ $title->title }}</h3>

                            <p class="mt-3 text-sm text-gray-500 font-medium">Adviser</p>
                            <p class="text-gray-800">{{ optional($title->adviserUser)->name ?? 'Unassigned' }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500 font-medium">Course</p>
                            <p class="text-gray-800 mt-1">{{ optional($title->course)->name }}</p>

                            <p class="mt-3 text-sm text-gray-500 font-medium">Number of Submissions</p>
                            <p class="text-gray-800">{{ $title->theses_count }}</p>
                        </div>
                    </div>


                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-700">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase">Team Members</p>
                            <div class="text-gray-900 space-y-1">
                                @forelse ($teamMembers as $memberName)
                                    <p>{{ $memberName }}</p>
                                @empty
                                    <p>N/A</p>
                                @endforelse
                            </div>
                        </div>

                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase">Leader</p>
                            <p class="text-gray-900">{{ $leaderName ?? 'Unassigned' }}{{ $isLeader ? ' (You)' : '' }}</p>
                        </div>
                    </div>


                    <div
                        class="flex flex-col sm:flex-row sm:justify-between sm:items-center border-t border-gray-100 pt-4">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-800 mb-3">Overall Defense Status</h2>
                            <div class="flex flex-wrap gap-4">

                                <div class="flex items-center gap-3">
                                    <div
                                        class="p-2 rounded-md {{ $titleDefenseReady ? 'bg-green-50' : 'bg-yellow-50' }}">
                                        <x-icon name="{{ $titleDefenseReady ? 'circle-check' : 'clock' }}"
                                            class="h-5 w-5 {{ $titleDefenseReady ? 'text-green-600' : 'text-yellow-600' }}" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">Title Defense</p>
                                        <p
                                            class="text-xs font-semibold {{ $titleDefenseReady ? 'text-green-700 bg-green-50' : 'text-yellow-700 bg-yellow-50' }}">
                                            Status: {{ $titleDefenseReady ? 'Ready' : 'Pending' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <div
                                        class="p-2 rounded-md {{ $finalDefenseReady ? 'bg-green-50' : 'bg-yellow-50' }}">
                                        <x-icon name="{{ $finalDefenseReady ? 'circle-check' : 'clock' }}"
                                            class="h-5 w-5 {{ $finalDefenseReady ? 'text-green-600' : 'text-yellow-600' }}" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">Final Defense</p>
                                        <p
                                            class="text-xs font-semibold {{ $finalDefenseReady ? 'text-green-700 bg-green-50' : 'text-yellow-700 bg-yellow-50' }}">
                                            Status: {{ $finalDefenseReady ? 'Ready' : 'Pending' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="mt-4 sm:mt-0">
                            <a href="{{ route('theses.show', $title) }}"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-[#050565] hover:bg-transparent hover:text-[#050565] border border-lg border-[#050565] transition rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#050565]">
                                <x-icon name="eye" class="h-4 w-4 mr-1" />
                                View Details
                            </a>
                        </div>
                    </div>


                    @if ($latestChapters->isNotEmpty())
                        <div>
                            <h2 class="text-sm font-semibold text-gray-800 mb-2">Chapters</h2>
                            <div class="flex flex-wrap gap-3">
                                @foreach ($latestChapters as $chapter)
                                    <div
                                        class="flex items-center gap-2 border border-gray-200 rounded-md px-3 py-2 bg-gray-50">
                                        <span class="text-xs font-medium text-gray-600">
                                            {{ \Illuminate\Support\Str::of($chapter->chapter_label)->replace('_', ' ')->replace('-', ' ')->title() }}
                                        </span>
                                        <span
                                            class="px-3 py-1 text-xs font-semibold rounded-md border
                                            {{ $chapter->status === 'approved'
                                                ? 'bg-green-50 border-green-200 text-green-700'
                                                : ($chapter->status === 'rejected'
                                                    ? 'bg-red-50 border-red-200 text-red-700'
                                                    : 'bg-yellow-50 border-yellow-200 text-yellow-700') }}">
                                            {{ ucfirst($chapter->status) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white shadow rounded-lg p-6 text-sm text-gray-600">
                    No thesis titles yet.
                </div>
            @endforelse

            <div class="mt-4">{{ $thesisTitles->links() }}</div>
        </div>
    </div>
</x-app-layout>
