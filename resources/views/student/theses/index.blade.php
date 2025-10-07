<x-app-layout>
    <x-slot name="header">
        My Submissions
    </x-slot>
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded bg-green-50 text-green-900 px-4 py-2">{{ session('status') }}</div>
            @endif

            <div class="flex items-center justify-end my-2">
                <a href="{{ route('theses.create') }}">
                    <x-primary-button type="button">
                        Submit a Thesis
                    </x-primary-button>
                </a>
            </div>

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
                    @endphp
                    <div class="bg-white shadow rounded-lg p-6 flex flex-col md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $title->title }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ optional($title->course)->name }}</p>
                            <p class="text-sm text-gray-500 mt-2">
                                Adviser: {{ optional($title->adviserUser)->name ?? 'Unassigned' }}
                            </p>
                            <p class="text-sm text-gray-500 mt-1">
                                Submissions: {{ $title->theses_count }}
                            </p>
                            @php
                                $titleDefenseReady = $title->titleDefenseApproved();
                                $finalDefenseReady = $title->chaptersAreApproved();
                            @endphp
                            <div class="mt-3 flex flex-wrap gap-2">
                                <span
                                    class="inline-flex items-center gap-2 rounded border px-3 py-1 text-xs font-semibold {{ $titleDefenseReady ? 'border-green-200 bg-green-50 text-green-700' : 'border-gray-200 bg-gray-50 text-gray-600' }}">
                                    @if ($titleDefenseReady)
                                        <svg class="h-4 w-4 text-green-500" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75 11.25 15 15 9.75" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18z" />
                                        </svg>
                                    @else
                                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l3 3" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
                                        </svg>
                                    @endif
                                    Title Defense: {{ $titleDefenseReady ? 'Ready' : 'Pending' }}
                                </span>
                                <span
                                    class="inline-flex items-center gap-2 rounded border px-3 py-1 text-xs font-semibold {{ $finalDefenseReady ? 'border-green-200 bg-green-50 text-green-700' : 'border-gray-200 bg-gray-50 text-gray-600' }}">
                                    @if ($finalDefenseReady)
                                        <svg class="h-4 w-4 text-green-500" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m9 12.75 1.5 1.5 3-3" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 21.75c-4.97-1.35-7.5-4.05-7.5-8.156V5.663L12 2.25l7.5 3.413v7.931c0 4.106-2.53 6.806-7.5 8.156z" />
                                        </svg>
                                    @else
                                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4.5 20.25a8.25 8.25 0 1 1 15 0" />
                                        </svg>
                                    @endif
                                    Final Defense: {{ $finalDefenseReady ? 'Ready' : 'Pending' }}
                                </span>
                            </div>
                            @if ($latestChapters->isNotEmpty())
                                <div class="mt-3">
                                    <p class="text-sm font-medium text-gray-700">Chapters</p>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @foreach ($latestChapters as $chapter)
                                            <span class="inline-flex items-center gap-2 rounded border border-gray-200 bg-gray-50 px-3 py-1 text-xs font-medium text-gray-700">
                                                <span>{{ \Illuminate\Support\Str::of($chapter->chapter_label ?? 'Submission')->replace('_', ' ')->replace('-', ' ')->title() }}</span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $statusColors[$chapter->status] ?? 'bg-gray-100 text-gray-700' }}">
                                                    {{ ucfirst($chapter->status) }}
                                                </span>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="mt-4 md:mt-0">
                            <a href="{{ route('theses.show', $title) }}" class="inline-flex items-center px-4 py-2 text-sm
                                font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md focus:outline-none
                                focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                View Details
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-white shadow rounded-lg p-6 text-sm text-gray-600">
                        No thesis titles yet.
                    </div>
                @endforelse
            </div>

            <div class="mt-4">{{ $thesisTitles->links() }}</div>
        </div>
    </div>
</x-app-layout>
