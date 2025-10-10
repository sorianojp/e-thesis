@php($routePrefix = auth()->user()->isAdmin() ? 'admin' : 'adviser')

<x-app-layout>
    <x-slot name="header">
        e-Thesis Review Queue
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded bg-green-50 text-green-900 px-4 py-2">{{ session('status') }}</div>
            @endif

            <div class="relative overflow-x-auto sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3">Title</th>
                            <th scope="col" class="px-6 py-3">Chapter</th>
                            <th scope="col" class="px-6 py-3">Student</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($theses as $t)
                            <tr class="bg-white border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-6 py-4">{{ $t->thesisTitle->title }}</td>
                                <td class="px-6 py-4">{{ $t->chapter_label }}</td>
                                <td class="px-6 py-4">{{ $t->thesisTitle->student->name }}</td>
                                <td class="px-6 py-4 capitalize font-bold">
                                    <span
                                        class="text-xs font-medium px-2.5 py-0.5 rounded-lg {{ $t->status === 'pending'
                                            ? 'bg-yellow-100 text-yellow-800'
                                            : ($t->status === 'approved'
                                                ? 'bg-green-100 text-green-800'
                                                : ($t->status === 'rejected'
                                                    ? 'bg-red-100 text-red-800'
                                                    : ($t->status === 'passed'
                                                        ? 'bg-blue-100 text-blue-800'
                                                        : ''))) }}">
                                        {{ $t->status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    @if (Auth::user()->can('review', $t) && in_array($t->status, ['pending', 'approved']))
                                        <a href="{{ route($routePrefix . '.theses.show', $t) }}">
                                            <x-primary-button type="button" class="gap-2">
                                                <x-icon name="eye" class="h-4 w-4" />
                                                {{ $t->status === 'approved' ? 'Re-review' : 'Review' }}
                                            </x-primary-button>
                                        </a>
                                    @elseif ($t->status === 'pending')
                                        <span class="text-sm text-gray-500">N/A</span>
                                    @elseif ($t->status === 'passed')
                                        <span class="text-sm text-gray-500">N/A</span>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-4 text-center text-sm text-gray-500" colspan="6">
                                    No theses to display.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $theses->links() }}</div>
        </div>
    </div>
</x-app-layout>
