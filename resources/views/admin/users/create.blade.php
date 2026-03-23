<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-red-600 leading-tight uppercase tracking-wide">
            Add New User
        </h2>
    </x-slot>

    <div class="py-12 max-w-5xl mx-auto sm:px-6 lg:px-8">

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-600 rounded">
                <div class="font-bold text-red-800 mb-1">Whoops! Something went wrong.</div>
                <ul class="list-disc list-inside text-sm text-red-700 ml-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('users.store') }}">
            @csrf

            <div class="bg-white p-8 rounded-lg shadow-sm border-l-4 border-gray-800 mb-8">
                <h3 class="text-lg font-black text-gray-800 mb-6 border-b pb-2 uppercase tracking-wide">Account Details</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Full Name <span class="text-red-600">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus class="w-full border-gray-300 rounded bg-gray-50 focus:bg-white focus:ring-red-500 focus:border-red-500 shadow-sm text-sm transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Email Address <span class="text-red-600">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full border-gray-300 rounded bg-gray-50 focus:bg-white focus:ring-red-500 focus:border-red-500 shadow-sm text-sm transition-colors">
                    </div>
                </div>

                <div class="p-5 bg-gray-50 border border-gray-200 rounded">
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mb-4">Security Credentials</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Password <span class="text-red-600">*</span></label>
                            <input type="password" name="password" required class="w-full border-gray-300 rounded focus:ring-red-500 focus:border-red-500 shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Confirm Password <span class="text-red-600">*</span></label>
                            <input type="password" name="password_confirmation" required class="w-full border-gray-300 rounded focus:ring-red-500 focus:border-red-500 shadow-sm text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-8 rounded-lg shadow-sm border-l-4 border-red-600 mb-8">
                <div class="flex justify-between items-end border-b pb-2 mb-6">
                    <h3 class="text-lg font-black text-gray-800 uppercase tracking-wide">Assign Features</h3>
                    <p class="text-xs text-gray-500 italic font-bold">Select the modules this user can access</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <label class="border p-4 rounded flex items-center gap-3 cursor-pointer hover:bg-red-50 transition-colors border-gray-200">
                        <input type="checkbox" name="permissions[]" value="manage-inventory" class="text-red-600 focus:ring-red-500 rounded h-5 w-5 border-gray-300">
                        <span class="text-sm font-bold text-gray-700">Manage Inventory & Stock</span>
                    </label>

                    <label class="border p-4 rounded flex items-center gap-3 cursor-pointer hover:bg-red-50 transition-colors border-gray-200">
                        <input type="checkbox" name="permissions[]" value="manage-printers" class="text-red-600 focus:ring-red-500 rounded h-5 w-5 border-gray-300">
                        <span class="text-sm font-bold text-gray-700">Manage Printers</span>
                    </label>

                    <label class="border p-4 rounded flex items-center gap-3 cursor-pointer hover:bg-red-50 transition-colors border-gray-200">
                        <input type="checkbox" name="permissions[]" value="manage-requests" class="text-red-600 focus:ring-red-500 rounded h-5 w-5 border-gray-300">
                        <span class="text-sm font-bold text-gray-700">Approve & Fulfill Requests</span>
                    </label>

                    <label class="border p-4 rounded flex items-center gap-3 cursor-pointer hover:bg-red-50 transition-colors border-gray-200">
                        <input type="checkbox" name="permissions[]" value="create-requests" class="text-red-600 focus:ring-red-500 rounded h-5 w-5 border-gray-300" checked>
                        <span class="text-sm font-bold text-gray-700">Submit Personal Requests</span>
                    </label>

                    <label class="border p-4 rounded flex items-center gap-3 cursor-pointer hover:bg-red-50 transition-colors border-gray-200">
                        <input type="checkbox" name="permissions[]" value="view-dashboard" class="text-red-600 focus:ring-red-500 rounded h-5 w-5 border-gray-300">
                        <span class="text-sm font-bold text-gray-700">View Analytics Dashboard</span>
                    </label>

                    <label class="border p-4 rounded flex items-center gap-3 cursor-pointer hover:bg-red-50 transition-colors border-gray-200">
                        <input type="checkbox" name="permissions[]" value="generate-reports" class="text-red-600 focus:ring-red-500 rounded h-5 w-5 border-gray-300">
                        <span class="text-sm font-bold text-gray-700">Generate PDF/Excel Reports</span>
                    </label>

                    <label class="border p-4 rounded flex items-center gap-3 cursor-pointer hover:bg-red-50 transition-colors border-gray-200">
                        <input type="checkbox" name="permissions[]" value="manage-users" class="text-red-600 focus:ring-red-500 rounded h-5 w-5 border-gray-300">
                        <span class="text-sm font-bold text-gray-700">Manage Users & Roles</span>
                    </label>

                    <label class="border p-4 rounded flex items-center gap-3 cursor-pointer hover:bg-red-50 transition-colors border-gray-200">
                        <input type="checkbox" name="permissions[]" value="manage-system" class="text-red-600 focus:ring-red-500 rounded h-5 w-5 border-gray-300">
                        <span class="text-sm font-bold text-gray-700">Manage System Settings (Types, Colors)</span>
                    </label>

                </div>
            </div>

            <div class="flex items-center gap-4 border-t pt-6">
                <button type="submit" class="px-8 py-3 bg-red-600 text-white font-black uppercase tracking-widest text-sm rounded shadow-lg hover:bg-red-700 hover:-translate-y-0.5 transition-all">
                    Create User Account
                </button>
                <a href="{{ route('users.index') }}" class="px-6 py-3 text-sm text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded font-bold uppercase tracking-widest transition-colors">
                    Cancel
                </a>
            </div>
        </form>

    </div>
</x-app-layout>
