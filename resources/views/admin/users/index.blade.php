<x-app-layout>
    <x-slot name="header">
        Manage Users
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded bg-green-50 text-green-900 px-4 py-2">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow sm:rounded p-6 mb-4">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col md:flex-row gap-3 md:items-center">
                    <div class="flex-1">
                        <x-input-label for="search" :value="__('Search by name or email')" />
                        <x-text-input id="search" name="search" type="text" class="block mt-1 w-full"
                            :value="$search" placeholder="Search..." />
                    </div>
                    <div class="flex gap-2">
                        <x-primary-button type="submit">Search</x-primary-button>
                        @if ($search)
                            <a href="{{ route('admin.users.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Clear</a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="relative overflow-x-auto sm:rounded-lg bg-white shadow">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs uppercase bg-gray-50 text-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3">Name</th>
                            <th scope="col" class="px-6 py-3">Email</th>
                            <th scope="col" class="px-6 py-3">Role</th>
                            <th scope="col" class="px-6 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr class="bg-white border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                                <td class="px-6 py-4">{{ $user->email }}</td>
                                <td class="px-6 py-4 capitalize">{{ $user->role }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-sm text-indigo-600 hover:underline">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $users->links() }}</div>
        </div>
    </div>
</x-app-layout>
