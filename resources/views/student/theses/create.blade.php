<x-app-layout>
    <x-slot name="header">
        Register Thesis Title
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded p-6">
                <form method="POST" action="{{ route('theses.store') }}" enctype="multipart/form-data"
                    class="thesis-title-form">
                    @csrf
                    <p class="text-sm text-gray-500 mb-4">Provide your thesis details, abstract, and endorsement letter.
                        You can upload thesis manuscripts after creating the title.</p>
                    <div class="mb-2">
                        <x-input-label for="title" :value="__('Title')" />
                        <x-text-input id="title" class="block mt-1 w-full" type="text" name="title"
                            :value="old('title')" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-2">
                            <x-input-label for="course_id" :value="__('Course')" />
                            <select name="course_id" id="course_id"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select a Course</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>{{ $course->name }}
                                    </option>
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-2">
                            <x-input-label for="abstract_pdf" :value="__('Abstract PDF')" />
                            <input id="abstract_pdf" type="file" name="abstract_pdf" accept="application/pdf,.pdf"
                                class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('abstract_pdf')" class="mt-2" />
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
                    <div class="mb-2">
                        <x-input-label for="members" :value="__('Team Members (optional)')" />
                        <p class="text-xs text-gray-500 mt-1">
                            Add up to {{ \App\Models\ThesisTitle::MAX_MEMBERS }} student members. Hold Ctrl (or Command on
                            Mac) to select multiple students.
                        </p>
                        <select name="members[]" id="members" multiple size="8"
                            data-max-members="{{ \App\Models\ThesisTitle::MAX_MEMBERS }}"
                            class="block mt-2 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}"
                                    @selected(collect(old('members'))->contains($student->id))>
                                    {{ $student->name }}
                                </option>
                            @endforeach
                        </select>
                        @if ($students->isEmpty())
                            <p class="text-xs text-gray-500 mt-1">No other students are available to add right now.</p>
                        @endif
                        <x-input-error :messages="$errors->get('members')" class="mt-2" />
                        <x-input-error :messages="$errors->get('members.*')" class="mt-2" />
                    </div>
                    <div class="flex justify-end items-center mt-4">
                        <x-primary-button type="submit" class="gap-2">
                            <x-icon name="check" class="h-4 w-4" />
                            Save Title
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('.thesis-title-form');
            const memberSelect = document.querySelector('#members');

            if (!form) {
                return;
            }

            if (memberSelect) {
                const maxMembers = Number(memberSelect.dataset.maxMembers || 0);
                memberSelect.addEventListener('change', () => {
                    if (!maxMembers) {
                        return;
                    }

                    const selected = Array.from(memberSelect.selectedOptions);
                    if (selected.length <= maxMembers) {
                        return;
                    }

                    const excess = selected.slice(maxMembers);
                    excess.forEach((option) => {
                        option.selected = false;
                    });

                    const message = `You can only add up to ${maxMembers} members.`;
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'info',
                            title: 'Team limit',
                            text: message,
                        });
                    } else {
                        alert(message);
                    }
                });
            }

            form.addEventListener('submit', (event) => {
                const fileInputs = form.querySelectorAll('input[type="file"]');
                const allFilesSelected = Array.from(fileInputs).every((input) => input.files && input.files.length);

                if (!allFilesSelected) {
                    return;
                }

                event.preventDefault();

                if (typeof Swal === 'undefined') {
                    form.submit();
                    return;
                }

                Swal.fire({
                    title: 'Saving thesis title...',
                    text: 'Please wait while we upload your files.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                        form.submit();
                    },
                });
            });
        });
    </script>
</x-app-layout>
