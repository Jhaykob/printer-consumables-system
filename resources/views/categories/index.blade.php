<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-red-600 leading-tight">Manage Categories</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @can('manage-assets')
            <div class="p-6 bg-white shadow sm:rounded-lg border-l-4 border-red-600">
                <form action="{{ route('categories.store') }}" method="POST" class="flex flex-col md:flex-row items-center gap-4">
                    @csrf
                    <div class="flex-1 w-full">
                        <x-text-input name="name" placeholder="Category Name (e.g. Toner)" class="w-full" required />
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="requires_color" id="requires_color" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <label for="requires_color" class="text-sm text-gray-700 font-medium">Requires Color?</label>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 font-bold">Add Category</button>
                </form>
            </div>
            @endcan

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-red-600 p-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Needs Color?</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                        <tr>
                            <td class="px-6 py-4 font-bold">{{ $category->name }}</td>
                            <td class="px-6 py-4">
                                @if($category->requires_color)
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full font-bold">Yes</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">No</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @can('manage-assets')
                                <form action="{{ route('categories.destroy', $category) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 font-bold hover:underline">Delete</button>
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
</x-app-layout>
