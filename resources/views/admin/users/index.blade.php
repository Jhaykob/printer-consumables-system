<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-red-600 leading-tight uppercase tracking-wide">User Management</h2>
            <a href="{{ route('users.create') }}" class="px-4 py-2 bg-red-600 text-white font-bold rounded shadow hover:bg-red-700 transition text-sm flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add New User
            </a>
        </div>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 font-bold rounded shadow-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 font-bold rounded shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="py-4 px-6 text-xs font-black text-gray-500 uppercase tracking-widest">Name</th>
                        <th class="py-4 px-6 text-xs font-black text-gray-500 uppercase tracking-widest">Email</th>
                        <th class="py-4 px-6 text-xs font-black text-gray-500 uppercase tracking-widest">Access Level</th>
                        <th class="py-4 px-6 text-xs font-black text-gray-500 uppercase tracking-widest">Status</th>
                        <th class="py-4 px-6 text-xs font-black text-gray-500 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-6">
                            <div class="font-bold text-gray-900">{{ $user->name }}</div>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-600">
                            {{ $user->email }}
                        </td>
                        <td class="py-4 px-6">
                            @if($user->is_superuser)
                                <span class="inline-flex items-center px-2.5 py-1 rounded bg-red-100 text-red-800 text-[10px] font-black uppercase tracking-widest border border-red-200">
                                    Full Administrator
                                </span>
                            @else
                                <div class="flex flex-wrap gap-1 max-w-xs">
                                    @forelse($user->permissions as $perm)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 text-gray-600 text-[10px] font-bold uppercase tracking-wider border border-gray-200">
                                            {{ str_replace('-', ' ', $perm->name) }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-400 italic">No Access Assigned</span>
                                    @endforelse
                                </div>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full {{ $user->is_active ?? true ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }} text-[10px] font-black uppercase tracking-widest">
                                {{ $user->is_active ?? true ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right space-x-3">
                            <a href="{{ route('users.edit', $user) }}" class="text-xs font-bold text-blue-600 hover:text-blue-900 uppercase tracking-wider">Edit</a>

                            @if(auth()->id() !== $user->id)
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to deactivate this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-red-600 hover:text-red-900 uppercase tracking-wider">Deactivate</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(method_exists($users, 'links'))
        <div class="mt-6">
            {{ $users->links() }}
        </div>
        @endif

    </div>
</x-app-layout>
