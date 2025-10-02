<x-app-layout>
    <x-slot name="header">
        Thesis Title Details
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded bg-green-50 text-green-900 px-4 py-2">{{ session('status') }}</div>
            @endif

            @php($latestThesis = $thesisTitle->theses->first())

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

                <div class="mt-4 flex flex-wrap gap-3 text-sm">
                    @if ($thesisTitle->abstract_pdf_path && $latestThesis)
                        <a class="text-indigo-600 hover:underline"
                            href="{{ route('theses.download', [$latestThesis, 'abstract']) }}">
                            Download Abstract PDF
                        </a>
                    @endif
                    @if ($thesisTitle->endorsement_pdf_path && $latestThesis)
                        <a class="text-indigo-600 hover:underline"
                            href="{{ route('theses.download', [$latestThesis, 'endorsement']) }}">
                            Download Endorsement Letter
                        </a>
                    @endif
                </div>
            </div>

            <div class="bg-white shadow sm:rounded p-6">
                <h3 class="text-lg font-semibold text-gray-900">Upload Thesis Manuscript</h3>
                <p class="text-sm text-gray-600 mt-1">Upload a PDF copy of your thesis. A new submission will reset
                    approvals and panel details.</p>
                <form method="POST" action="{{ route('theses.upload', $thesisTitle) }}" class="mt-4"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="flex flex-col md:flex-row md:items-center gap-4">
                        <div class="flex-1">
                            <input type="file" name="thesis_pdf" id="thesis_pdf" required
                                accept="application/pdf,.pdf"
                                class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                            <x-input-error :messages="$errors->get('thesis_pdf')" class="mt-2" />
                        </div>
                        <x-primary-button type="submit">Upload Thesis</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow sm:rounded p-6">
                <h3 class="text-lg font-semibold text-gray-900">Submissions</h3>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm text-left text-gray-600">
                        <thead class="text-xs uppercase bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-4 py-2">Uploaded</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Plagiarism</th>
                                <th class="px-4 py-2">Remarks</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($thesisTitle->theses as $thesis)
                                <tr class="border-b border-gray-100">
                                    <td class="px-4 py-3">{{ $thesis->created_at->format('F d, Y h:i A') }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="px-2.5 py-0.5 rounded text-xs font-medium capitalize {{ $thesis->status === 'pending'
                                                ? 'bg-yellow-100 text-yellow-800'
                                                : ($thesis->status === 'approved'
                                                    ? 'bg-green-100 text-green-800'
                                                    : ($thesis->status === 'rejected'
                                                        ? 'bg-red-100 text-red-800'
                                                        : ($thesis->status === 'passed'
                                                            ? 'bg-blue-100 text-blue-800'
                                                            : 'bg-gray-100 text-gray-700'))) }}">
                                            {{ $thesis->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-plagiarism-summary :thesis="$thesis" :compact="true" />
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">
                                        {{ $thesis->adviser_remarks ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 space-y-1">
                                        <a class="text-indigo-600 hover:underline block"
                                            href="{{ route('theses.download', [$thesis, 'thesis']) }}">Download</a>
                                        @if (in_array($thesis->status, ['approved', 'passed'], true))
                                            <a class="text-indigo-600 hover:underline block"
                                                href="{{ route('theses.certificate', $thesis) }}">Certificate</a>
                                        @endif
                                        @if ($thesis->status === 'passed' && !is_null($thesisTitle->grade))
                                            <a class="text-indigo-600 hover:underline block"
                                                href="{{ route('theses.approval', $thesis) }}">Approval Sheet</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-4 py-3" colspan="5">No thesis submissions yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
