<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-red-600 leading-tight uppercase tracking-wide">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-red-600 mb-8">
            <div class="p-8 border-b border-gray-200">
                <h3 class="text-2xl font-black text-gray-900 tracking-tight uppercase">Welcome back, {{ auth()->user()->name }}!</h3>
                <p class="mt-1 text-gray-500 font-medium text-sm">Select an action below to manage your printer consumables.</p>
            </div>
        </div>

        @php
            $user = auth()->user();
            $userPerms = $user->permissions ? $user->permissions->pluck('name')->toArray() : [];
            $isSuper = $user->is_superuser;
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <div class="w-12 h-12 bg-red-50 text-red-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <h4 class="text-lg font-black text-gray-800 uppercase tracking-wide mb-2">Request Supplies</h4>
                    <p class="text-xs text-gray-500 mb-6 leading-relaxed">Need toner or imaging drums? Submit a new request to the inventory team.</p>
                </div>
                <a href="{{ route('requests.create') }}" class="block w-full text-center px-4 py-2 bg-red-600 text-white font-bold uppercase tracking-widest text-xs rounded shadow-sm hover:bg-red-700 transition-colors">
                    New Request
                </a>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <div class="w-12 h-12 bg-gray-50 text-gray-600 rounded-lg flex items-center justify-center mb-4 border border-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                    <h4 class="text-lg font-black text-gray-800 uppercase tracking-wide mb-2">My Requests</h4>
                    <p class="text-xs text-gray-500 mb-6 leading-relaxed">Check the status of your past and pending supply requests.</p>
                </div>
                <a href="{{ route('requests.index') }}" class="block w-full text-center px-4 py-2 bg-gray-100 text-gray-700 font-bold uppercase tracking-widest text-xs rounded hover:bg-gray-200 transition-colors">
                    View History
                </a>
            </div>

            @if($isSuper || in_array('manage-requests', $userPerms))
            <div class="bg-white p-6 rounded-lg shadow-sm border-t-4 border-red-600 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-lg flex items-center justify-center border border-orange-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <span class="px-2 py-1 bg-orange-100 text-orange-800 text-[10px] font-black uppercase tracking-widest rounded-full">Action Needed</span>
                    </div>
                    <h4 class="text-lg font-black text-gray-800 uppercase tracking-wide mb-2">Fulfill Requests</h4>
                    <p class="text-xs text-gray-500 mb-6 leading-relaxed">Review and approve pending consumable requests from staff.</p>
                </div>
                <a href="{{ route('admin.requests.index') }}" class="block w-full text-center px-4 py-2 bg-gray-800 text-white font-bold uppercase tracking-widest text-xs rounded shadow-sm hover:bg-gray-700 transition-colors">
                    Manage Queue
                </a>
            </div>
            @endif

            @if($isSuper || in_array('manage-inventory', $userPerms))
            <div class="bg-white p-6 rounded-lg shadow-sm border-t-4 border-red-600 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <div class="w-12 h-12 bg-red-50 text-red-600 rounded-lg flex items-center justify-center mb-4 border border-red-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <h4 class="text-lg font-black text-gray-800 uppercase tracking-wide mb-2">Inventory Stock</h4>
                    <p class="text-xs text-gray-500 mb-6 leading-relaxed">Monitor current stock levels and update inventory thresholds.</p>
                </div>
                <a href="{{ route('inventory.index') }}" class="block w-full text-center px-4 py-2 bg-gray-800 text-white font-bold uppercase tracking-widest text-xs rounded shadow-sm hover:bg-gray-700 transition-colors">
                    View Inventory
                </a>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
