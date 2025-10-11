<x-app-layout>
    <x-slot name="header">
        {{ __('Dashboard') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto space-y-8 sm:px-6 lg:px-8">
            @if ($thesisStats)
                <section class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">Your Thesis Overview</h2>
                     <h3 class="text-sm text-gray-600">Here's a summary of your thesis
                        submissions.</h3>
                    <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3">
                        <div
                            class=" rounded-xl border border-gray-200 bg-white p-5 shadow-sm flex justify-between items-center hover:shadow-md transition">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 bg-blue-50 rounded-lg">
                                    <x-icon name="upload" class="w-6 h-6 text-blue-500" />
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Theses Uploaded</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $thesisStats['uploaded'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div
                            class=" rounded-xl border border-gray-200 bg-white p-5 shadow-sm flex justify-between items-center hover:shadow-md transition">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 bg-blue-50 rounded-lg">
                                    <x-icon name="book-open" class="w-6 h-6 text-blue-500" />
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Titles Led</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $thesisStats['leader_titles'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div
                            class=" rounded-xl border border-gray-200 bg-white p-5 shadow-sm flex justify-between items-center hover:shadow-md transition">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 bg-blue-50 rounded-lg">
                                    <x-icon name="user-group" class="w-6 h-6 text-blue-500" />
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Team Titles</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $thesisStats['team_titles'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div
                            class=" rounded-xl border border-gray-200 bg-white p-5 shadow-sm flex justify-between items-center hover:shadow-md transition">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 bg-yellow-50 rounded-lg">
                                    <x-icon name="clock" class="w-6 h-6 text-yellow-500" />
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Pending</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $thesisStats['pending'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div
                            class=" rounded-xl border border-gray-200 bg-white p-5 shadow-sm flex justify-between items-center hover:shadow-md transition">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 bg-green-50 rounded-lg">
                                    <x-icon name="circle-check" class="w-6 h-6 text-green-500" />
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Approved</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $thesisStats['approved'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div
                            class=" rounded-xl border border-gray-200 bg-white p-5 shadow-sm flex justify-between items-center hover:shadow-md transition">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 bg-red-50 rounded-lg">
                                    <x-icon name="x-mark" class="w-6 h-6 text-red-500" />
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Rejected</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $thesisStats['rejected'] }}</p>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="rounded-lg border border-gray-200 bg-white p-6 text-center shadow-sm">
                            <h3 class="text-sm font-medium text-gray-500">Theses Uploaded</h3>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $thesisStats['uploaded'] }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white p-6 text-center shadow-sm">
                            <h3 class="text-sm font-medium text-gray-500">Titles Led</h3>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $thesisStats['leader_titles'] }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white p-6 text-center shadow-sm">
                            <h3 class="text-sm font-medium text-gray-500">Team Titles</h3>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $thesisStats['team_titles'] }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white p-6 text-center shadow-sm">
                            <h3 class="text-sm font-medium text-gray-500">Pending</h3>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $thesisStats['pending'] }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white p-6 text-center shadow-sm">
                            <h3 class="text-sm font-medium text-gray-500">Approved</h3>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $thesisStats['approved'] }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white p-6 text-center shadow-sm">
                            <h3 class="text-sm font-medium text-gray-500">Rejected</h3>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $thesisStats['rejected'] }}</p>
                        </div> --}}
                    </div>
                </section>
            @endif

            @if ($adviserStats)
                <section class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">Advisee Thesis Overview</h2>
                    <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-3">
                        <div
                            class=" rounded-xl border border-gray-200 bg-white p-5 shadow-sm flex justify-between items-center hover:shadow-md transition">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 bg-blue-50 rounded-lg">
                                    <x-icon name="upload" class="w-6 h-6 text-blue-500" />
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Theses</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $adviserStats['theses'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div
                            class=" rounded-xl border border-gray-200 bg-white p-5 shadow-sm flex justify-between items-center hover:shadow-md transition">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 bg-blue-50 rounded-lg">
                                    <x-icon name="book-open" class="w-6 h-6 text-blue-500" />
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Thesis Titles</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $adviserStats['thesis_titles'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div
                            class=" rounded-xl border border-gray-200 bg-white p-5 shadow-sm flex justify-between items-center hover:shadow-md transition">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 bg-blue-50 rounded-lg">
                                    <x-icon name="user-group" class="w-6 h-6 text-blue-500" />
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Students</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $adviserStats['students'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div
                            class=" rounded-xl border border-gray-200 bg-white p-5 shadow-sm flex justify-between items-center hover:shadow-md transition">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 bg-yellow-50 rounded-lg">
                                    <x-icon name="clock" class="w-6 h-6 text-yellow-500" />
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Rejected</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $adviserStats['pending'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div
                            class=" rounded-xl border border-gray-200 bg-white p-5 shadow-sm flex justify-between items-center hover:shadow-md transition">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 bg-green-50 rounded-lg">
                                    <x-icon name="circle-check" class="w-6 h-6 text-green-500" />
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Approved</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $adviserStats['approved'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div
                            class=" rounded-xl border border-gray-200 bg-white p-5 shadow-sm flex justify-between items-center hover:shadow-md transition">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 bg-red-50 rounded-lg">
                                    <x-icon name="x-mark" class="w-6 h-6 text-red-500" />
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Rejected</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $adviserStats['rejected'] }}</p>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="rounded-lg border border-gray-200 bg-white p-6 text-center shadow-sm">
                            <h3 class="text-sm font-medium text-gray-500">Theses</h3>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $adviserStats['theses'] }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white p-6 text-center shadow-sm">
                            <h3 class="text-sm font-medium text-gray-500">Thesis Titles</h3>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $adviserStats['thesis_titles'] }}
                            </p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white p-6 text-center shadow-sm">
                            <h3 class="text-sm font-medium text-gray-500">Students</h3>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $adviserStats['students'] }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white p-6 text-center shadow-sm">
                            <h3 class="text-sm font-medium text-gray-500">Pending</h3>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $adviserStats['pending'] }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white p-6 text-center shadow-sm">
                            <h3 class="text-sm font-medium text-gray-500">Approved</h3>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $adviserStats['approved'] }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white p-6 text-center shadow-sm">
                            <h3 class="text-sm font-medium text-gray-500">Rejected</h3>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $adviserStats['rejected'] }}</p>
                        </div> --}}
                    </div>
                </section>
            @endif

            @if ($adminStats)
                <section class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">System Overview</h2>
                    <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
                        <div class="rounded-lg border border-gray-200 bg-white p-6 text-center shadow-sm">
                            <h3 class="text-sm font-medium text-gray-500">Theses</h3>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $adminStats['theses'] }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white p-6 text-center shadow-sm">
                            <h3 class="text-sm font-medium text-gray-500">Postgrad Theses</h3>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $adminStats['postgrad_theses'] }}
                            </p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white p-6 text-center shadow-sm">
                            <h3 class="text-sm font-medium text-gray-500">Users</h3>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $adminStats['users'] }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white p-6 text-center shadow-sm">
                            <h3 class="text-sm font-medium text-gray-500">Students</h3>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $adminStats['students'] }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white p-6 text-center shadow-sm">
                            <h3 class="text-sm font-medium text-gray-500">Advisers</h3>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $adminStats['advisers'] }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white p-6 text-center shadow-sm">
                            <h3 class="text-sm font-medium text-gray-500">Pending Theses</h3>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $adminStats['pending'] }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white p-6 text-center shadow-sm">
                            <h3 class="text-sm font-medium text-gray-500">Approved Theses</h3>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $adminStats['approved'] }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white p-6 text-center shadow-sm">
                            <h3 class="text-sm font-medium text-gray-500">Rejected Theses</h3>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $adminStats['rejected'] }}</p>
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
