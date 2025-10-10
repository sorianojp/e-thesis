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
                    <p class="mb-1"><b>Chapter:</b> {{ $thesis->chapter_label }}</p>
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
                                        : '')) }}">
                            {{ $thesis->status }}
                        </span>
                    </p>
                @php($approvalEligible = $approvalEligible ?? false)
                @php($approvalSheetThesis = $approvalSheetThesis ?? null)
                @php($titleDefenseReady = $thesis->thesisTitle->titleDefenseApproved())
                @php($finalDefenseReady = $thesis->thesisTitle->chaptersAreApproved())
                @php($firstApproved = $thesis->thesisTitle->theses->first(fn ($chapter) => $chapter->status === 'approved'))

                <div class="text-sm text-gray-500 mb-1">
                    All grading is now handled offline; certificates will display a blank grade line.
                </div>
                <div class="mt-3 flex flex-wrap gap-2 text-sm text-gray-600">
                    <span>Title Defense: {{ $titleDefenseReady ? 'Ready' : 'Pending' }} • Final Defense: {{ $finalDefenseReady ? 'Ready' : 'Pending' }}</span>
                    @if ($titleDefenseReady && $firstApproved)
                        <a class="text-indigo-600 hover:underline"
                            href="{{ route('theses.certificate', [$firstApproved, 'stage' => 'title']) }}">Title Defense Certificate</a>
                    @endif
                    @if ($finalDefenseReady && $firstApproved)
                        <a class="text-indigo-600 hover:underline"
                            href="{{ route('theses.certificate', [$firstApproved, 'stage' => 'final']) }}">Final Defense Certificate</a>
                    @endif
                    @if ($approvalEligible && $approvalSheetThesis)
                        <a class="text-indigo-600 hover:underline"
                            href="{{ route('theses.approval', $approvalSheetThesis) }}">Download Approval Sheet</a>
                    @endif
                </div>
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
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                                <b>Panel Details</b>
                                @if ($titleDefenseReady && $firstApproved)
                                    @can('review', $thesis)
                                        <a href="{{ route($routePrefix . '.theses.panel.edit', $thesis) }}"
                                            class="text-sm text-indigo-600 hover:underline">Edit</a>
                                    @endcan
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Panel assignment unlocks once Chapters 1–3 are approved for Title Defense.</p>
                            @if ($titleDefenseReady && $firstApproved)
                                @if ($thesis->thesisTitle->panel_chairman || $thesis->thesisTitle->panelist_one || $thesis->thesisTitle->panelist_two || $thesis->thesisTitle->defense_date)
                                    <dl class="mt-3 space-y-1 text-sm text-gray-700">
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
                                    <p class="text-sm text-gray-500 mt-3">Panel details pending.</p>
                                @endif
                            @else
                                <p class="text-sm text-gray-500 mt-3">Await approval of Chapters 1–3 to enable panel assignment.</p>
                            @endif
                        </div>
                    @endif
                </div>

                @can('review', $thesis)
                    <div class="flex flex-wrap gap-2">
                        <form method="POST" action="{{ route($routePrefix . '.theses.approve', $thesis) }}">
                            @csrf
                            <x-primary-button type="submit" class="gap-2">
                                <x-icon name="check" class="h-4 w-4" />
                                Approve
                            </x-primary-button>
                        </form>
                        <form method="POST" action="{{ route($routePrefix . '.theses.reject', $thesis) }}">
                            @csrf
                            <x-danger-button type="submit" class="gap-2">
                                <x-icon name="x-mark" class="h-4 w-4" />
                                Reject
                            </x-danger-button>
                        </form>
                    </div>
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
