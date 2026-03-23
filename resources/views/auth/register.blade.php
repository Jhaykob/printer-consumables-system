<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Full Name</label>
            <input id="name" class="block w-full border-gray-300 rounded-md bg-gray-50 focus:bg-white focus:ring-red-500 focus:border-red-500 shadow-sm text-sm transition-colors" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-600 text-xs" />
        </div>

        <div class="mt-5">
            <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Email Address</label>
            <input id="email" class="block w-full border-gray-300 rounded-md bg-gray-50 focus:bg-white focus:ring-red-500 focus:border-red-500 shadow-sm text-sm transition-colors" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 text-xs" />
        </div>

        <div class="mt-5">
            <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Password</label>
            <input id="password" class="block w-full border-gray-300 rounded-md bg-gray-50 focus:bg-white focus:ring-red-500 focus:border-red-500 shadow-sm text-sm transition-colors" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 text-xs" />
        </div>

        <div class="mt-5">
            <label for="password_confirmation" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Confirm Password</label>
            <input id="password_confirmation" class="block w-full border-gray-300 rounded-md bg-gray-50 focus:bg-white focus:ring-red-500 focus:border-red-500 shadow-sm text-sm transition-colors" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-600 text-xs" />
        </div>

        <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-100">
            <a class="text-xs text-gray-500 hover:text-red-600 font-bold underline transition-colors" href="{{ route('login') }}">
                Already registered?
            </a>

            <button type="submit" class="px-6 py-2.5 bg-red-600 text-white font-black uppercase tracking-widest text-xs rounded shadow-md hover:bg-red-700 hover:-translate-y-0.5 transition-all">
                Register
            </button>
        </div>
    </form>
</x-guest-layout>
