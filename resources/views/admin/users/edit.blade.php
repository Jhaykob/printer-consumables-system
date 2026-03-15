<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary leading-tight">
            Manage Permissions for {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('users.update', $user) }}" method="POST" class="bg-white p-8 shadow-sm sm:rounded-lg border-l-4 border-primary">
                @csrf
                @method('PUT')

                <h3 class="text-lg font-bold mb-4 text-gray-700">Assign Features</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($permissions as $permission)
                    <div class="flex items-center space-x-3 p-3 border rounded hover:bg-red-50 transition">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                            id="perm_{{ $permission->id }}"
                            {{ $user->hasPermission($permission->name) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-primary focus:ring-primary">
                        <label for="perm_{{ $permission->id }}" class="text-gray-700 cursor-pointer">
                            {{ $permission->display_name }}
                        </label>
                    </div>
                    @endforeach
                </div>

                <div class="mt-8 flex items-center space-x-4">
                    <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-6 rounded shadow transition">
                        Save Changes
                    </button>
                    <a href="{{ route('users.index') }}" class="text-gray-500 hover:underline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
