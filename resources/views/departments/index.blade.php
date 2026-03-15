<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-red-600 leading-tight">
            {{ __('Manage Departments') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @can('manage-assets')
            <div class="p-6 bg-white shadow sm:rounded-lg border-l-4 border-red-600">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Department</h3>
                <form action="{{ route('departments.store') }}" method="POST" class="flex flex-col md:flex-row gap-4 items-center">
                    @csrf
                    <div class="flex-1 w-full">
                        <x-text-input name="name" placeholder="Department Name (e.g. Human Resources)" class="w-full" required />
                    </div>
                    <button type="submit" class="inline-flex items-center px-6 py-2 bg-red-600 border border-transparent rounded-md font-bold text-white uppercase hover:bg-red-700 transition">
                        Save Department
                    </button>
                </form>
            </div>
            @endcan

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-red-600">
                <div class="p-6 text-gray-900">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department Name</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($departments as $dept)
                            <tr>
                                <td class="px-6 py-4 font-bold">{{ $dept->name }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    @can('manage-assets')
                                    <form action="{{ route('departments.destroy', $dept) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:text-red-900 font-bold" onclick="return confirm('Delete this department?')">Delete</button>
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
