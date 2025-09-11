{{-- resources/views/student/theses/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Submit e-Thesis</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded p-6">
                <form method="POST" action="{{ route('theses.store') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <div>
                        <x-input-label for="version" :value="__('Version')" />
                        <x-text-input id="version" class="block mt-1 w-sm" type="text" name="version"
                            :value="old('version')" autofocus />
                        <x-input-error :messages="$errors->get('version')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="title" :value="__('Title')" />
                        <x-text-input id="title" class="block mt-1 w-full" type="text" name="title"
                            :value="old('title')" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
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
                        <div>
                            <x-input-label for="adviser" :value="__('Adviser')" />
                            <x-text-input id="adviser" class="block mt-1 w-full" type="text" name="adviser"
                                :value="old('adviser')" />
                            <x-input-error :messages="$errors->get('adviser')" class="mt-2" />
                        </div>
                    </div>
                    <div>
                        <div>
                            <x-input-label for="abstract" :value="__('Abstract')" />
                            <textarea id="abstract"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                type="text" name="abstract" :value="old('abstract')"></textarea>
                            <x-input-error :messages="$errors->get('abstract')" class="mt-2" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="thesis_pdf" :value="__('Theses')" />
                            <input id="thesis_pdf" type="file" name="thesis_pdf" accept="application/pdf"
                                class="mt-1 block w-full" />
                            @error('thesis_pdf')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <x-input-label for="endorsement_pdf" :value="__('Endorsement Letter')" />
                            <input id="endorsement_pdf" type="file" name="endorsement_pdf" accept="application/pdf"
                                class="mt-1 block w-full" />
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
