<x-app-layout>
    <x-slot name="header">
        Upload Postgraduate Thesis
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded p-6">
                <form method="POST" action="{{ route('admin.postgrad.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="title" :value="__('Title')" />
                        <x-text-input id="title" name="title" type="text" class="block mt-1 w-full"
                            :value="old('title')" autofocus />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="adviser" :value="__('Adviser')" />
                        <x-text-input id="adviser" name="adviser" type="text" class="block mt-1 w-full"
                            :value="old('adviser')" />
                        <x-input-error :messages="$errors->get('adviser')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="thesis_pdf" :value="__('Thesis PDF')" />
                        <input id="thesis_pdf" name="thesis_pdf" type="file" accept="application/pdf,.pdf"
                            class="mt-1 block w-full" />
                        <p class="text-xs text-gray-500 mt-1">Accepted format: PDF (max 40 MB)</p>
                        <x-input-error :messages="$errors->get('thesis_pdf')" class="mt-2" />
                    </div>

                    <div class="flex justify-between items-center">
                        <a href="{{ route('admin.postgrad.index') }}" class="text-sm text-gray-600 hover:underline">Back to list</a>
                        <x-primary-button type="submit">Upload</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
