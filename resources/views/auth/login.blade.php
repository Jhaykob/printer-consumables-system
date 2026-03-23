<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Email Address</label>
            <input id="email" class="block w-full border-gray-300 rounded-md bg-gray-50 focus:bg-white focus:ring-red-500 focus:border-red-500 shadow-sm text-sm transition-colors" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 text-xs" />
        </div>

        <div class="mt-6">
            <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Password</label>
            <input id="password" class="block w-full border-gray-300 rounded-md bg-gray-50 focus:bg-white focus:ring-red-500 focus:border-red-500 shadow-sm text-sm transition-colors" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 text-xs" />
        </div>

        <div class="block mt-6">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500" name="remember">
                <span class="ml-2 text-sm text-gray-600 font-bold group-hover:text-gray-900 transition-colors">Remember me</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-100">
            @if (Route::has('password.request'))
                <a class="text-xs text-gray-500 hover:text-red-600 font-bold underline transition-colors" href="{{ route('password.request') }}">
                    Forgot your password?
                </a>
            @else
                <div></div> @endif

            <button type="submit" class="px-6 py-2.5 bg-red-600 text-white font-black uppercase tracking-widest text-xs rounded shadow-md hover:bg-red-700 hover:-translate-y-0.5 transition-all">
                Log in
            </button>
        </div>
    </form>
</x-guest-layout>
