<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-red-600 leading-tight">
            {{ __('Manage Consumable Types') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @can('manage-assets')
            <div class="p-6 bg-white shadow sm:rounded-lg border-l-4 border-red-600">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Register New Type</h3>

                @if($categories->isEmpty())
                    <div class="text-red-500 mb-4 font-bold">Please add a Category before creating a Consumable Type.</div>
                @else
                    <form action="{{ route('consumable-types.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        @csrf

                        <div>
                            <select name="category_id" class="w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm" required>
                                <option value="" disabled selected>Select Category...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-text-input name="name" placeholder="Model Name (e.g. HP 30A)" class="w-full" required />
                        </div>

                        <div class="md:col-span-2 flex space-x-4">
                            <x-text-input name="description" placeholder="Short Description (Optional)" class="w-full" />
                            <button type="submit" class="whitespace-nowrap inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition">
                                Save Type
                            </button>
                        </div>
                    </form>
                @endif
            </div>
            @endcan

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-red-600">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type / Model Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($types as $type)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                                    {{ $type->category->name }}
                                    @if($type->category->requires_color)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Color Req.</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $type->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $type->description ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @can('manage-assets')
                                    <form action="{{ route('consumable-types.destroy', $type) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:text-red-900 font-bold" onclick="return confirm('Delete this Consumable Type?')">Delete</button>
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
