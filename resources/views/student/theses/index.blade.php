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
                    @php($latest = $title->theses->first())
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
                            <p class="text-sm text-gray-500 mt-1">
                                Latest Status:
                                @if ($latest)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium capitalize {{ $latest->status === 'pending'
                                            ? 'bg-yellow-100 text-yellow-800'
                                            : ($latest->status === 'approved'
                                                ? 'bg-green-100 text-green-800'
                                                : ($latest->status === 'rejected'
                                                    ? 'bg-red-100 text-red-800'
                                                    : ($latest->status === 'passed'
                                                        ? 'bg-blue-100 text-blue-800'
                                                        : 'bg-gray-100 text-gray-700'))) }}">
                                        {{ $latest->status }}
                                    </span>
                                @else
                                    <span class="text-gray-500">No thesis uploaded yet</span>
                                @endif
                            </p>
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
