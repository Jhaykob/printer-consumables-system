<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome - Printer Consumables Portal</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,700,900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 text-gray-800 font-sans selection:bg-red-600 selection:text-white flex flex-col min-h-screen">

    <nav class="bg-white border-b-4 border-red-600 shadow-sm relative z-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 flex items-center justify-center w-12 h-12 bg-red-50 text-red-600 rounded-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-gray-900 tracking-tight uppercase leading-none">Printer <span class="text-red-600">Consumables</span></h1>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Management System</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-sm font-bold text-gray-600 hover:text-red-600 uppercase tracking-wide transition-colors">Go to Dashboard &rarr;</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-bold text-gray-600 hover:text-red-600 uppercase tracking-wide transition-colors">Log in</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-5 py-2.5 bg-red-600 text-white text-sm font-bold uppercase tracking-wide rounded shadow-md hover:bg-red-700 hover:-translate-y-0.5 transition-all">Register</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-full max-w-7xl h-full pointer-events-none opacity-40 flex justify-between z-0">
            <div class="w-64 h-64 bg-red-100 rounded-full blur-3xl -ml-32 mt-12"></div>
            <div class="w-96 h-96 bg-gray-200 rounded-full blur-3xl -mr-32 mb-12 self-end"></div>
        </div>

        <div class="max-w-3xl mx-auto text-center relative z-10 bg-white/80 backdrop-blur-sm p-10 sm:p-16 rounded-2xl shadow-xl border border-gray-100">

            <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black text-gray-900 tracking-tighter uppercase mb-6 leading-tight">
                Printer Consumables <br/>
                <span class="text-red-600">Portal</span>
            </h2>

            <p class="mt-4 text-base sm:text-lg text-gray-500 font-medium max-w-xl mx-auto mb-10 leading-relaxed">
                Request toner, ink, and other printer supplies quickly and easily. Log in to track your requests and get what you need to keep working.
            </p>

            <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto px-10 py-4 bg-red-600 text-white font-black text-sm uppercase tracking-widest rounded shadow-lg hover:bg-red-700 hover:shadow-xl hover:-translate-y-1 transition-all flex justify-center items-center gap-2">
                            Access Dashboard
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-10 py-4 bg-red-600 text-white font-black text-sm uppercase tracking-widest rounded shadow-lg hover:bg-red-700 hover:shadow-xl hover:-translate-y-1 transition-all flex justify-center items-center gap-2">
                            Login to Request Supplies
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </main>

    <footer class="bg-white border-t border-gray-200 mt-auto relative z-20">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">&copy; {{ date('Y') }} Printer Consumables Management System. Internal Use Only.</p>
        </div>
    </footer>

</body>
</html>
