<x-app-layout>
    <x-slot name="header">
        Review
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded bg-green-50 text-green-900 px-4 py-2">{{ session('status') }}</div>
            @endif

            <div class="grid grid-cols-2 gap-2">

                <div class="bg-white shadow sm:rounded p-6 col-span-2">
                    <p class="mb-1"><b>Student:</b> {{ $thesis->student->name }} ({{ $thesis->student->email }})</p>
                    <p class="mb-1"><b>Course:</b> {{ $thesis->course->name }}</p>
                    <p class="mb-1"><b>Title:</b> {{ $thesis->title }}</p>
                    <p class="mb-1"><b>Version:</b> {{ $thesis->version }}</p>
                    <p class="mb-1"><b>Adviser:</b>
                        {{ optional($thesis->adviserUser)->name ?? $thesis->adviser ?? 'Unassigned' }}
                    </p>
                    <p class="capitalize"><b>Status:</b>
                        <span
                            class="text-xs font-medium px-2.5 py-0.5 rounded-lg {{ $thesis->status === 'pending'
                                ? 'bg-yellow-100 text-yellow-800'
                                : ($thesis->status === 'approved'
                                    ? 'bg-green-100 text-green-800'
                                    : ($thesis->status === 'rejected'
                                        ? 'bg-red-100 text-red-800'
                                        : '')) }}">
                            {{ $thesis->status }}
                        </span>
                    </p>
                    <hr class="my-6" />
                    <div>
                        <p class="mb-1"><b>Attachments:</b></p>
                        <a class="text-blue-700 hover:underline mr-4"
                            href="{{ route('theses.download', [$thesis, 'thesis']) }}">View
                            Thesis PDF</a>
                        <a class="text-blue-700 hover:underline"
                            href="{{ route('theses.download', [$thesis, 'endorsement']) }}">View Endorsement</a>
                    </div>

                    @if ($thesis->abstract_pdf_path || $thesis->abstract)
                        <div class="mt-6 border rounded p-4 bg-gray-50">
                            <b>Abstract</b>
                            @if ($thesis->abstract_pdf_path)
                                <p class="mt-2">
                                    <a class="text-blue-700 hover:underline"
                                        href="{{ route('theses.download', [$thesis, 'abstract']) }}">Download Abstract</a>
                                </p>
                            @else
                                <p class="whitespace-pre-line mt-1">{{ $thesis->abstract }}</p>
                            @endif
                        </div>
                    @endif
                </div>

                @can('review', $thesis)
                    <form method="POST" action="{{ route('admin.theses.approve', $thesis) }}"
                        class="bg-white shadow sm:rounded p-6">
                        @csrf
                        <h3 class="font-semibold mb-2">Approve</h3>
                        <textarea name="admin_remarks" rows="3" placeholder="Optional remarks" class="w-full rounded border-gray-300"></textarea>
                        <div class='flex justify-end'>
                            <x-primary-button type="submit">Approve</x-primary-button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('admin.theses.reject', $thesis) }}"
                        class="bg-white shadow sm:rounded p-6">
                        @csrf
                        <h3 class="font-semibold mb-2">Reject</h3>
                        <textarea name="admin_remarks" rows="3" placeholder="Required remarks" class="w-full rounded border-gray-300"></textarea>
                        @error('admin_remarks')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                        <div class='flex justify-end'>
                            <x-danger-button type="submit">Reject</x-danger-button>
                        </div>
                    </form>
                @else
                    <div class="bg-white shadow sm:rounded p-6 col-span-2">
                        <p class="text-sm text-gray-600">Viewing only. Approval actions are reserved for the assigned adviser.</p>
                    </div>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>
