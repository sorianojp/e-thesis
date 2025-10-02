@php($routePrefix = auth()->user()->isAdmin() ? 'admin' : 'adviser')

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
                    <p class="mb-1"><b>Student:</b> {{ $thesis->thesisTitle->student->name }} ({{ $thesis->thesisTitle->student->email }})</p>
                    <p class="mb-1"><b>Course:</b> {{ optional($thesis->thesisTitle->course)->name }}</p>
                    <p class="mb-1"><b>Title:</b> {{ $thesis->thesisTitle->title }}</p>
                    <p class="mb-1"><b>Adviser:</b>
                        {{ optional($thesis->thesisTitle->adviserUser)->name ?? 'Unassigned' }}
                    </p>
                    <p class="capitalize"><b>Status:</b>
                        <span
                            class="text-xs font-medium px-2.5 py-0.5 rounded-lg {{ $thesis->status === 'pending'
                                ? 'bg-yellow-100 text-yellow-800'
                                : ($thesis->status === 'approved'
                                    ? 'bg-green-100 text-green-800'
                                    : ($thesis->status === 'rejected'
                                        ? 'bg-red-100 text-red-800'
                                        : ($thesis->status === 'passed'
                                            ? 'bg-blue-100 text-blue-800'
                                            : ''))) }}">
                            {{ $thesis->status }}
                        </span>
                    </p>
                    @if (!is_null($thesis->thesisTitle->grade))
                        <p class="mb-1"><b>Grade:</b> {{ number_format((float) $thesis->thesisTitle->grade, 2) }}</p>
                    @endif
                    <hr class="my-6" />
                    <div>
                        <p class="mb-1"><b>Attachments:</b></p>
                        <a class="text-blue-700 hover:underline mr-4"
                            href="{{ route('theses.download', [$thesis, 'thesis']) }}">Thesis</a>
                        <a class="text-blue-700 hover:underline mr-4"
                            href="{{ route('theses.download', [$thesis, 'endorsement']) }}">Endorsement</a>
                        <a class="text-blue-700 hover:underline"
                            href="{{ route('theses.download', [$thesis, 'abstract']) }}">Abstract</a>
                    </div>

                    <hr class="my-6" />
                    <div>
                        <p class="mb-1"><b>Plagiarism Scan:</b></p>
                        <x-plagiarism-summary :thesis="$thesis" />
                    </div>

                    @if ($thesis->status === 'approved')
                        <div class="mt-6 border rounded p-4 bg-gray-50">
                            <div class="flex justify-between items-start">
                                <b>Panel Details</b>
                                @can('review', $thesis)
                                    <a href="{{ route($routePrefix . '.theses.panel.edit', $thesis) }}"
                                        class="text-sm text-indigo-600 hover:underline">Edit</a>
                                @endcan
                            </div>
                            @if ($thesis->thesisTitle->panel_chairman || $thesis->thesisTitle->panelist_one || $thesis->thesisTitle->panelist_two || $thesis->thesisTitle->defense_date)
                                <dl class="mt-2 space-y-1 text-sm text-gray-700">
                                    @if ($thesis->thesisTitle->panel_chairman)
                                        <div><span class="font-semibold">Chairman:</span> {{ $thesis->thesisTitle->panel_chairman }}
                                        </div>
                                    @endif
                                    @if ($thesis->thesisTitle->panelist_one)
                                        <div><span class="font-semibold">Panelist 1:</span> {{ $thesis->thesisTitle->panelist_one }}
                                        </div>
                                    @endif
                                    @if ($thesis->thesisTitle->panelist_two)
                                        <div><span class="font-semibold">Panelist 2:</span> {{ $thesis->thesisTitle->panelist_two }}
                                        </div>
                                    @endif
                                    @if ($thesis->thesisTitle->defense_date)
                                        <div><span class="font-semibold">Defense Date:</span>
                                            {{ $thesis->thesisTitle->defense_date->format('F d, Y') }}
                                        </div>
                                    @endif
                                </dl>
                            @else
                                <p class="text-sm text-gray-500 mt-2">Panel details pending.</p>
                            @endif
                        </div>
                    @endif
                </div>

                @can('review', $thesis)
                    @if ($thesis->status === 'approved')
                        <form method="POST" action="{{ route($routePrefix . '.theses.grade', $thesis) }}"
                            class="bg-white shadow sm:rounded p-6">
                            @csrf
                            <h3 class="font-semibold mb-2">Mark as Passed</h3>
                            <div class="mb-3">
                                <label for="grade" class="block text-sm font-medium text-gray-700">Grade</label>
                                <input type="number" step="0.01" min="0" max="100" name="grade"
                                    id="grade" value="{{ old('grade', $thesis->thesisTitle->grade) }}"
                                    class="mt-1 w-full rounded border-gray-300" required>
                                @error('grade')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class='flex justify-end'>
                                <x-primary-button type="submit">Save Grade</x-primary-button>
                            </div>
                        </form>
                    @endif

                    <form method="POST" action="{{ route($routePrefix . '.theses.approve', $thesis) }}"
                        class="bg-white shadow sm:rounded p-6">
                        @csrf
                        <h3 class="font-semibold mb-2">Approve</h3>
                        <textarea name="adviser_remarks" rows="3" placeholder="Optional remarks" class="w-full rounded border-gray-300"></textarea>
                        <div class='flex justify-end'>
                            <x-primary-button type="submit">Approve</x-primary-button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route($routePrefix . '.theses.reject', $thesis) }}"
                        class="bg-white shadow sm:rounded p-6">
                        @csrf
                        <h3 class="font-semibold mb-2">Reject</h3>
                        <textarea name="adviser_remarks" rows="3" placeholder="Required remarks" class="w-full rounded border-gray-300"></textarea>
                        @error('adviser_remarks')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                        <div class='flex justify-end'>
                            <x-danger-button type="submit">Reject</x-danger-button>
                        </div>
                    </form>
                @else
                    <div class="bg-white shadow sm:rounded p-6 col-span-2">
                        <p class="text-sm text-gray-600">Viewing only. Approval actions are reserved for the assigned
                            adviser.</p>
                    </div>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>
