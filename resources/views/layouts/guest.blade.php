<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Printer Consumables System') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,900&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-50 selection:bg-red-600 selection:text-white relative overflow-hidden">

        <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-full max-w-7xl h-full pointer-events-none opacity-40 flex justify-between z-0">
            <div class="w-64 h-64 bg-red-100 rounded-full blur-3xl -ml-32 mt-12"></div>
            <div class="w-96 h-96 bg-gray-200 rounded-full blur-3xl -mr-32 mb-12 self-end"></div>
        </div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative z-10">
            <div>
                <a href="/" class="flex flex-col items-center gap-3 group">
                    <div class="flex items-center justify-center w-16 h-16 bg-red-50 text-red-600 rounded-xl group-hover:bg-red-100 transition-colors shadow-sm border border-red-100">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    </div>
                    <div class="text-center mt-2">
                        <h1 class="text-2xl font-black text-gray-900 tracking-tight uppercase leading-none">Printer <span class="text-red-600">Consumables</span></h1>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Management System</p>
                    </div>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-8 px-8 py-10 bg-white/90 backdrop-blur-sm shadow-xl border-t-4 border-red-600 overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>

            <div class="mt-8 text-center">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">&copy; {{ date('Y') }} Internal Use Only.</p>
            </div>
        </div>
    </body>
</html>
