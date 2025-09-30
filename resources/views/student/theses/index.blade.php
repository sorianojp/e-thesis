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

            <div class="relative overflow-x-auto sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3">Version</th>
                            <th scope="col" class="px-6 py-3">Title</th>
                            <th scope="col" class="px-6 py-3">Course</th>
                            <th scope="col" class="px-6 py-3">Attachments</th>
                            <th scope="col" class="px-6 py-3">Plagiarism</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Certificate / Approval</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($theses as $t)
                            <tr class="bg-white border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-6 py-4">{{ $t->version }}</td>
                                <td class="px-6 py-4">{{ $t->title }}</td>
                                <td class="px-6 py-4">{{ $t->course->name }}</td>
                                <td class="px-6 py-4">
                                    <a class="text-blue-700 hover:underline"
                                        href="{{ route('theses.download', [$t, 'thesis']) }}">Thesis</a><br />
                                    <a class="text-blue-700 hover:underline"
                                        href="{{ route('theses.download', [$t, 'endorsement']) }}">Endorsement</a><br />
                                    @if ($t->abstract_pdf_path)
                                        <a class="text-blue-700 hover:underline"
                                            href="{{ route('theses.download', [$t, 'abstract']) }}">Abstract</a><br />
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if ($t->plagiarism_status === 'completed')
                                        <span class="font-semibold text-gray-900">
                                            {{ number_format((float) $t->plagiarism_score, 2) }}%
                                        </span>
                                        @if ($t->plagiarism_checked_at)
                                            <span class="block text-xs text-gray-500">
                                                Checked {{ $t->plagiarism_checked_at->diffForHumans() }}
                                            </span>
                                        @endif
                                    @elseif ($t->plagiarism_status === 'failed')
                                        <span class="text-red-600">Scan failed</span>
                                    @elseif ($t->plagiarism_status === 'skipped')
                                        <span class="text-yellow-600">Not scanned</span>
                                    @else
                                        <span class="text-gray-600">
                                            {{ \Illuminate\Support\Str::headline($t->plagiarism_status ?? 'pending') }}
                                        </span>
                                    @endif
                                </td>
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
                                    @if ($t->status === 'passed' && !is_null($t->grade))
                                        <a class="text-blue-700 hover:underline"
                                            href="{{ route('theses.approval', $t) }}">Approval Sheet</a>
                                    @elseif ($t->status === 'approved')
                                        <a class="text-blue-700 hover:underline"
                                            href="{{ route('theses.certificate', $t) }}">Certificate to Defend</a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-4" colspan="7">No submissions yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $theses->links() }}</div>
        </div>
    </div>
</x-app-layout>
