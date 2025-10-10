<x-app-layout>
    <x-slot name="header">
        Advisee Titles
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded bg-green-50 text-green-900 px-4 py-2">{{ session('status') }}</div>
            @endif

            <div class="space-y-4">
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
                            'passed' => 'bg-blue-100 text-blue-800',
                        ];

                        $titleDefenseReady = $title->titleDefenseApproved();
                        $finalDefenseReady = $title->chaptersAreApproved();
                        $thesisCount = $title->theses_count ?? $title->theses->count();
                    @endphp
                    <div
                        class="bg-white shadow rounded-lg p-6 flex flex-col md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $title->title }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ optional($title->course)->name }}</p>
                            <p class="text-sm text-gray-500 mt-2">Student: {{ optional($title->student)->name }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                Submissions: {{ $thesisCount }}
                            </p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <span
                                    class="inline-flex items-center gap-2 rounded border px-3 py-1 text-xs font-semibold {{ $titleDefenseReady ? 'border-green-200 bg-green-50 text-green-700' : 'border-gray-200 bg-gray-50 text-gray-600' }}">
                                    @if ($titleDefenseReady)
                                        <x-icon name="circle-check" class="h-4 w-4 text-green-500" />
                                    @else
                                        <x-icon name="clock" class="h-4 w-4 text-gray-400" />
                                    @endif
                                    Title Defense: {{ $titleDefenseReady ? 'Ready' : 'Pending' }}
                                </span>
                                <span
                                    class="inline-flex items-center gap-2 rounded border px-3 py-1 text-xs font-semibold {{ $finalDefenseReady ? 'border-green-200 bg-green-50 text-green-700' : 'border-gray-200 bg-gray-50 text-gray-600' }}">
                                    @if ($finalDefenseReady)
                                        <x-icon name="shield-check" class="h-4 w-4 text-green-500" />
                                    @else
                                        <x-icon name="users" class="h-4 w-4 text-gray-400" />
                                    @endif
                                    Final Defense: {{ $finalDefenseReady ? 'Ready' : 'Pending' }}
                                </span>
                            </div>
                            @if ($latestChapters->isNotEmpty())
                                <div class="mt-3">
                                    <p class="text-sm font-medium text-gray-700">Chapters</p>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @foreach ($latestChapters as $chapter)
                                            <span
                                                class="inline-flex items-center gap-2 rounded border border-gray-200 bg-gray-50 px-3 py-1 text-xs font-medium text-gray-700">
                                                <span>{{ \Illuminate\Support\Str::of($chapter->chapter_label ?? 'Submission')->replace('_', ' ')->replace('-', ' ')->title() }}</span>
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $statusColors[$chapter->status] ?? 'bg-gray-100 text-gray-700' }}">
                                                    {{ ucfirst($chapter->status) }}
                                                </span>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="mt-4 md:mt-0">
                            <a href="{{ route('adviser.theses.show', $title) }}"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <x-icon name="eye" class="h-4 w-4" /> Review Chapters
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-white shadow rounded-lg p-6 text-sm text-gray-600">
                        No assigned thesis titles yet.
                    </div>
                @endforelse
            </div>

            <div class="mt-4">{{ $thesisTitles->links() }}</div>
        </div>
    </div>
</x-app-layout>
