<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-red-600 leading-tight">
            {{ __('Printer Locations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- ADD THIS NEW ERROR DISPLAY BLOCK --}}
            {{-- @error('delete_error')
                <div class="p-4 bg-red-100 border-l-4 border-red-600 text-red-800 font-bold rounded shadow-sm">
                    {{ $message }}
                </div>
            @enderror --}}

            {{-- Keep your existing status message if you have one --}}
            {{-- @if (session('status'))
                <div class="p-4 bg-green-100 border-l-4 border-green-600 text-green-800 font-bold rounded shadow-sm">
                    {{ session('status') }}
                </div>
            @endif --}}

            @can('manage-assets')
                <div class="p-6 bg-white shadow sm:rounded-lg border-l-4 border-red-600">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Location</h3>
                    <form action="{{ route('locations.store') }}" method="POST" class="flex flex-col md:flex-row gap-4">
                        @csrf
                        <div class="flex-1">
                            <x-text-input name="name" placeholder="Location Name (e.g. Accounts Office)" class="w-full"
                                required />
                        </div>
                        <div class="flex-1">
                            <x-text-input name="description" placeholder="Description (Optional)" class="w-full" />
                        </div>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Save Location
                        </button>
                    </form>
                </div>
            @endcan

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-red-600">
                <div class="p-6 text-gray-900">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($locations as $location)
                                <tr>
                                    <td class="px-6 py-4 font-bold">{{ $location->name }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $location->description }}</td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        @can('manage-assets')
                                            <form action="{{ route('locations.destroy', $location) }}" method="POST"
                                                class="inline">
                                                @csrf @method('DELETE')
                                                <button class="text-red-600 hover:text-red-900 text-sm font-bold"
                                                    onclick="return confirm('Delete this location?')">Delete</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
