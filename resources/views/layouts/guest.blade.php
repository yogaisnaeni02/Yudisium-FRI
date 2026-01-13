<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Laravel')) - SIYU</title>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ file_exists(public_path('favicon.ico')) ? asset('favicon.ico') : asset('images/logo/fri-icon.png') }}">
        <link rel="apple-touch-icon" href="{{ file_exists(public_path('images/logo/fri-icon.png')) ? asset('images/logo/fri-icon.png') : '' }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gradient-to-br from-green-50 via-white to-green-50">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <!-- Logo Section -->
            <div class="mb-8">
                <a href="/" class="flex items-center gap-3 group">
                    <x-fri-icon size="lg" class="group-hover:scale-105 transition-transform" />
                    <div>
                        <div class="text-2xl font-bold text-gray-900">SIYU</div>
                        <div class="text-sm text-gray-600">Sistem Informasi Yudisium</div>
                    </div>
                </a>
            </div>

            <!-- Card Container -->
            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-2xl overflow-hidden sm:rounded-2xl border border-green-100">
                {{ $slot }}
            </div>

            <!-- Footer Links -->
            <div class="mt-8 text-center space-y-4">
                <a href="/" class="text-sm text-gray-600 hover:text-green-700 font-medium transition">
                    Kembali ke Beranda
                </a>
                <div class="pt-4 border-t border-gray-200">
                    <p class="text-xs text-gray-500">
                        &copy; {{ date('Y') }} SIYU - Sistem Informasi Yudisium. All rights reserved.
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        Fakultas Rekayasa Industri - Telkom University
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
