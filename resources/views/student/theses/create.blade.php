<x-app-layout>
    <x-slot name="header">
        Submit e-Thesis
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded p-6">
                <form method="POST" action="{{ route('theses.store') }}" enctype="multipart/form-data">
                    @csrf
                    <p class="text-sm text-gray-500 mb-4">Version numbers are assigned automatically with each
                        submission.</p>
                    <div class="mb-2">
                        <x-input-label for="title" :value="__('Title')" />

                        <div x-data="{
                            inputValue: '{{ old('title') }}',
                            suggestions: [],
                            showSuggestions: false,
                            allTitles: @js($previousTitles), // All titles passed from PHP
                        
                            // Function to filter titles based on current input value
                            filterSuggestions() {
                                // Check if input is empty or has content
                                if (this.inputValue.length === 0) {
                                    // If input is empty, show ALL titles (the 'reverse' part)
                                    this.suggestions = this.allTitles;
                                } else {
                                    // If input has content, filter the titles
                                    this.suggestions = this.allTitles.filter(title =>
                                        title.toLowerCase().includes(this.inputValue.toLowerCase())
                                    ).slice(0, 10); // Limit results for performance
                                }
                        
                                // Show suggestions only if there are results
                                this.showSuggestions = this.suggestions.length > 0;
                            },
                        
                            // Action when a suggestion is clicked
                            selectSuggestion(title) {
                                this.inputValue = title;
                                this.showSuggestions = false;
                            },
                        
                            // Action when the input field is focused (clicked)
                            showAllSuggestionsOnFocus() {
                                // Immediately show all titles (filterSuggestions will handle this)
                                this.filterSuggestions();
                                this.showSuggestions = true;
                            }
                        }" @click.outside="showSuggestions = false" class="relative">

                            {{-- Input Field --}}
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title"
                                x-model="inputValue" {{-- Bind input value --}} @input.debounce.200ms="filterSuggestions()"
                                {{-- Filter as user types (with a slight delay) --}} @focus="showAllSuggestionsOnFocus()" {{-- Show all titles when input is clicked/focused --}}
                                autocomplete="off" />

                            {{-- Custom Full-Width Suggestion Dropdown --}}
                            <ul x-cloak x-show="showSuggestions"
                                class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">

                                <template x-for="title in suggestions" :key="title">
                                    <li @click="selectSuggestion(title)"
                                        class="p-2 text-gray-800 cursor-pointer hover:bg-indigo-500 hover:text-white transition duration-150 ease-in-out"
                                        x-text="title">
                                    </li>
                                </template>

                                <li x-show="suggestions.length === 0" class="p-2 text-gray-500 italic">
                                    No matching titles found.
                                </li>
                            </ul>

                            {{-- Hidden input to ensure the form submits the value bound to the Alpine state --}}
                            <input type="hidden" name="title" :value="inputValue">
                        </div>

                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-2">
                            <x-input-label for="course_id" :value="__('Course')" />
                            <select name="course_id" id="course_id" :value="old('course_id')"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select a Course</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('course_id')" class="mt-2" />
                        </div>
                        <div class="mb-2">
                            <x-input-label for="adviser_id" :value="__('Adviser')" />
                            <select name="adviser_id" id="adviser_id" required
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select an Adviser</option>
                                @foreach ($advisers as $adviser)
                                    <option value="{{ $adviser->id }}" @selected(old('adviser_id') == $adviser->id)>
                                        {{ $adviser->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($advisers->isEmpty())
                                <p class="text-xs text-gray-500 mt-1">No advisers available yet. Please contact an
                                    administrator.</p>
                            @endif
                            <x-input-error :messages="$errors->get('adviser_id')" class="mt-2" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="mb-2">
                            <x-input-label for="abstract_pdf" :value="__('Abstract')" />
                            <input id="abstract_pdf" type="file" name="abstract_pdf" accept="application/pdf,.pdf"
                                class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('abstract_pdf')" class="mt-2" />
                        </div>
                        <div class="mb-2">
                            <x-input-label for="thesis_pdf" :value="__('Theses')" />
                            <input id="thesis_pdf" type="file" name="thesis_pdf" accept="application/pdf,.pdf"
                                class="mt-1 block w-full" required />
                            @error('thesis_pdf')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-2">
                            <x-input-label for="endorsement_pdf" :value="__('Endorsement Letter')" />
                            <input id="endorsement_pdf" type="file" name="endorsement_pdf"
                                accept="application/pdf,.pdf" class="mt-1 block w-full" required />
                            @error('endorsement_pdf')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="flex justify-end items-center mt-4">
                        <x-primary-button type="submit">Submit</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
