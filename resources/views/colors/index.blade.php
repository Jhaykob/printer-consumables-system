<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-red-600 leading-tight">Manage Colors</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @error('is_na') <div class="text-red-600 font-bold">{{ $message }}</div> @enderror
            @error('delete') <div class="text-red-600 font-bold">{{ $message }}</div> @enderror

            @can('manage-assets')
            <div class="p-6 bg-white shadow sm:rounded-lg border-l-4 border-red-600">
                <form action="{{ route('colors.store') }}" method="POST" class="flex flex-col md:flex-row items-center gap-4">
                    @csrf
                    <div class="flex-1 w-full">
                        <x-text-input name="name" placeholder="Color Name (e.g. Cyan)" class="w-full" required />
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="is_na" id="is_na" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <label for="is_na" class="text-sm text-gray-700 font-medium">Is "N/A" Base Color?</label>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 font-bold">Add Color</button>
                </form>
            </div>
            @endcan

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-red-600 p-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Color Name</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($colors as $color)
                        <tr>
                            <td class="px-6 py-4 font-bold">
                                {{ $color->name }}
                                @if($color->is_na) <span class="ml-2 px-2 py-1 bg-gray-200 text-xs rounded">System Default (N/A)</span> @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @can('manage-assets')
                                    @if(!$color->is_na)
                                    <form action="{{ route('colors.destroy', $color) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 font-bold hover:underline">Delete</button>
                                    </form>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
