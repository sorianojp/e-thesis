@php($routePrefix = auth()->user()->isAdmin() ? 'admin' : 'adviser')

<x-app-layout>
    <x-slot name="header">
        Panel Assignment
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded bg-green-50 text-green-900 px-4 py-2 mb-4">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow sm:rounded p-6">
                <div class="mb-6">
                    <p class="font-semibold">{{ $thesis->thesisTitle->title }}</p>
                    <p class="text-sm text-gray-600">{{ $thesis->thesisTitle->student->name }} •
                        {{ optional($thesis->thesisTitle->course)->name }}</p>
                </div>

                <form method="POST" action="{{ route($routePrefix . '.theses.panel.update', $thesis) }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="panel_chairman" :value="__('Panel Chairman')" />
                            <x-text-input id="panel_chairman" class="block mt-1 w-full" type="text" name="panel_chairman"
                                :value="old('panel_chairman', $thesis->thesisTitle->panel_chairman)" autofocus />
                            <x-input-error :messages="$errors->get('panel_chairman')" class="mt-2" />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="panelist_one" :value="__('Panelist 1')" />
                                <x-text-input id="panelist_one" class="block mt-1 w-full" type="text" name="panelist_one"
                                    :value="old('panelist_one', $thesis->thesisTitle->panelist_one)" />
                                <x-input-error :messages="$errors->get('panelist_one')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="panelist_two" :value="__('Panelist 2')" />
                                <x-text-input id="panelist_two" class="block mt-1 w-full" type="text" name="panelist_two"
                                    :value="old('panelist_two', $thesis->thesisTitle->panelist_two)" />
                                <x-input-error :messages="$errors->get('panelist_two')" class="mt-2" />
                            </div>
                        </div>
                        <div>
                            <x-input-label for="defense_date" :value="__('Defense Date')" />
                            <x-text-input id="defense_date" class="block mt-1 w-full" type="date" name="defense_date"
                                :value="old('defense_date', optional($thesis->thesisTitle->defense_date)?->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('defense_date')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex justify-end items-center mt-6 space-x-2">
                        <a href="{{ route($routePrefix . '.theses.show', $thesis) }}" class="text-sm text-gray-600 hover:underline">Back to thesis</a>
                        <x-primary-button type="submit" class="gap-2">
                            <x-icon name="check" class="h-4 w-4" />
                            Save Panel
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
