<x-app-layout>
    <x-slot name="header">
        Postgraduate Theses
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded bg-green-50 text-green-900 px-4 py-2">{{ session('status') }}</div>
            @endif

            <div class="flex justify-end mb-4">
                <a href="{{ route('admin.postgrad.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Upload Thesis
                </a>
            </div>

            <div class="relative overflow-x-auto sm:rounded-lg bg-white shadow">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs uppercase bg-gray-50 text-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3">Title</th>
                            <th scope="col" class="px-6 py-3">Adviser</th>
                            <th scope="col" class="px-6 py-3">Uploaded</th>
                            <th scope="col" class="px-6 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($theses as $postgrad)
                            <tr class="bg-white border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $postgrad->title }}</td>
                                <td class="px-6 py-4">{{ $postgrad->adviser }}</td>
                                <td class="px-6 py-4">{{ optional($postgrad->created_at)->format('F d, Y h:i A') }}</td>
                                <td class="px-6 py-4 text-right">
                                    @if ($postgrad->thesis_pdf_path)
                                        <a href="{{ route('admin.postgrad.download', $postgrad) }}" class="text-sm text-indigo-600 hover:underline">Download</a>
                                    @else
                                        <span class="text-xs text-gray-400">No file</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No postgraduate theses uploaded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $theses->links() }}</div>
        </div>
    </div>
</x-app-layout>
