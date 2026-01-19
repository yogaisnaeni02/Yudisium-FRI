<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Dashboard') - SIYU</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <div class="min-h-screen flex w-full">
            <!-- Left Sidebar -->
            <aside class="w-64 bg-white border-r border-gray-200 hidden md:block fixed h-screen">
                <div class="px-6 py-6 border-b border-gray-200">
                    <x-fri-logo size="sm" />
                </div>
                <nav class="px-4 py-6 space-y-1 overflow-y-auto h-[calc(100vh-100px)]">
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-green-600 text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }} transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <span>Dashboard Admin</span>
                        </a>
                        <a href="{{ route('admin.verifikasi-pengajuan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.verifikasi-pengajuan') || request()->routeIs('admin.submission-detail') ? 'bg-green-600 text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }} transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            <span>Verifikasi Pengajuan</span>
                        </a>
                        <a href="{{ route('admin.articles') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.articles*') ? 'bg-green-600 text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }} transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                            </svg>
                            <span>Informasi Yudisium</span>
                        </a>
                        <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.users*') ? 'bg-green-600 text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }} transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span>Manajemen User</span>
                        </a>
                        <a href="{{ route('admin.periodes') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.periodes*') ? 'bg-green-600 text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }} transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>Manajemen Periode</span>
                        </a>
                        <a href="{{ route('admin.yudisium-sidings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.yudisium-sidings*') ? 'bg-green-600 text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }} transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>Sidang Yudisium</span>
                        </a>
                        <a href="{{ route('admin.dosens') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.dosens*') ? 'bg-green-600 text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }} transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span>Database Dosen</span>
                        </a>
                    @else
                        <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('student.dashboard') ? 'bg-green-600 text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }} transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('student.pengajuan-yudisium') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('student.pengajuan-yudisium') ? 'bg-green-600 text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }} transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                            </svg>
                            <span>Pengajuan Yudisium</span>
                        </a>
                        <a href="{{ route('student.articles') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('student.articles*') ? 'bg-green-600 text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }} transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                            </svg>
                            <span>Informasi Yudisium</span>
                        </a>
                    @endif

                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <div class="text-xs text-gray-500 uppercase font-semibold mb-2 px-3">Support</div>
                        <a href="{{ route('help.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('help.index') ? 'bg-green-600 text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }} transition text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Help & Support
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('profile.edit') ? 'bg-green-600 text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }} transition text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Settings
                        </a>
                    </div>
                </nav>
            </aside>

            <!-- Main Content Area -->
            <div class="flex-1 md:ml-64 flex flex-col w-full">
                <!-- Professional Top Bar -->
                <header class="bg-white border-b border-gray-200 z-40 shadow-sm">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center justify-between h-16">
                            <!-- Left: Page Title & Breadcrumb -->
                            <div class="flex items-center gap-4">
                                <!-- Mobile menu button -->
                                <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                    </svg>
                                </button>
                                
                                <div>
                                    <h1 class="text-xl font-bold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                                    @hasSection('breadcrumb')
                                        <nav class="flex text-sm text-gray-500 mt-1">
                                            @yield('breadcrumb')
                                        </nav>
                                    @endif
                                </div>
                            </div>

                            <!-- Right: User Menu & Actions -->
                            <div class="flex items-center gap-4">
                                <!-- Notifications -->
                                <div x-data="notifications()" class="relative">
                                    <button @click="openModal()" class="relative p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                        </svg>
                                        <span x-show="unreadCount > 0" class="absolute top-1 right-1 w-2 h-2 bg-green-600 rounded-full"></span>
                                    </button>

                                    <!-- Notifications Modal -->
                                    <div x-show="showModal" 
                                         @click.away="closeModal()"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg border border-gray-200 z-50 max-h-96 overflow-hidden flex flex-col">
                                        
                                        <!-- Modal Header -->
                                        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                                            <h3 class="text-sm font-semibold text-gray-900">Notifikasi</h3>
                                            <button @click="closeModal()" class="text-gray-500 hover:text-gray-700">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Notifications List -->
                                        <div class="overflow-y-auto flex-1">
                                            <template x-if="notificationList.length === 0">
                                                <div class="p-8 text-center text-gray-500">
                                                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                                    </svg>
                                                    <p class="text-sm">Tidak ada notifikasi</p>
                                                </div>
                                            </template>

                                            <template x-if="notificationList.length > 0">
                                                <div class="divide-y divide-gray-100">
                                                    <template x-for="notif in notificationList" :key="notif.id">
                                                        <div :class="notif.is_read ? 'bg-white' : 'bg-blue-50'" class="px-4 py-3 hover:bg-gray-50 cursor-pointer transition flex justify-between items-start gap-2">
                                                            <div class="flex-1" @click="markAsRead(notif)">
                                                                <div class="flex items-start gap-2">
                                                                    <div :class="notif.is_read ? 'bg-gray-300' : 'bg-blue-600'" class="w-2 h-2 rounded-full mt-2 flex-shrink-0"></div>
                                                                    <div class="flex-1">
                                                                        <p class="text-sm font-medium text-gray-900" x-text="notif.title"></p>
                                                                        <p class="text-sm text-gray-600 mt-0.5" x-text="notif.message"></p>
                                                                        <p class="text-xs text-gray-400 mt-1" x-text="formatDate(notif.created_at)"></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Modal Footer -->
                                        <template x-if="notificationList.length > 0">
                                            <div class="px-4 py-2 border-t border-gray-200 bg-gray-50">
                                                <div class="flex gap-2">
                                                    <button @click="markAllAsRead()" class="flex-1 text-sm text-center text-blue-600 hover:text-blue-700 font-medium py-1">
                                                        Tandai semua sebagai sudah dibaca
                                                    </button>
                                                    <button @click="deleteAllNotifications()" class="flex-1 text-sm text-center text-red-600 hover:text-red-700 font-medium py-1">
                                                        Hapus semua notifikasi
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- User Dropdown -->
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-100 transition">
                                        <div class="hidden sm:block text-right">
                                            <div class="text-sm font-semibold text-gray-900">
                                                @if(Auth::user()->role === 'admin')
                                                    {{ Auth::user()->name }}
                                                @else
                                                    @php
                                                        $studentName = isset($student) ? ($student->nama ?? $student->user->name ?? Auth::user()->name) : Auth::user()->name;
                                                    @endphp
                                                    {{ $studentName }}
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                @if(Auth::user()->role === 'admin')
                                                    Administrator
                                                @else
                                                    @php
                                                        $studentNim = isset($student) ? ($student->nim ?? 'Mahasiswa') : 'Mahasiswa';
                                                    @endphp
                                                    {{ $studentNim }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center text-white font-semibold shadow-lg overflow-hidden">
                                            @if(Auth::user()->foto)
                                                <img src="{{ Storage::url(Auth::user()->foto) }}?t={{ time() }}" alt="Profile" class="w-full h-full object-cover">
                                            @else
                                                @if(Auth::user()->role === 'admin')
                                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                                @else
                                                    @php
                                                        $studentName = isset($student) ? ($student->nama ?? $student->user->name ?? Auth::user()->name) : Auth::user()->name;
                                                    @endphp
                                                    {{ strtoupper(substr($studentName, 0, 1)) }}
                                                @endif
                                            @endif
                                        </div>
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div x-show="open" 
                                         @click.away="open = false"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                                        <div class="px-4 py-3 border-b border-gray-200">
                                            <p class="text-sm font-semibold text-gray-900">
                                                @if(Auth::user()->role === 'admin')
                                                    {{ Auth::user()->name }}
                                                @else
                                                    @php
                                                        $studentName = isset($student) ? ($student->nama ?? $student->user->name ?? Auth::user()->name) : Auth::user()->name;
                                                    @endphp
                                                    {{ $studentName }}
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                        </div>
                                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            Profile Settings
                                        </a>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                                </svg>
                                                Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="w-full flex-1 overflow-x-hidden">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
                        @if(session('success'))
                            <x-alert type="success" dismissible>{{ session('success') }}</x-alert>
                        @endif
                        
                        @if(session('error'))
                            <x-alert type="error" dismissible>{{ session('error') }}</x-alert>
                        @endif
                        
                        @if(session('warning'))
                            <x-alert type="warning" dismissible>{{ session('warning') }}</x-alert>
                        @endif
                        
                        @if(session('info'))
                            <x-alert type="info" dismissible>{{ session('info') }}</x-alert>
                        @endif
                        
                        @if($errors->any())
                            <x-alert type="error">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </x-alert>
                        @endif

                        @yield('content')
                    </div>
                </main>


            </div>
        </div>

        <!-- Mobile Sidebar Overlay -->
        <div id="mobile-sidebar-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"></div>
        
        <!-- Mobile Sidebar -->
        <aside id="mobile-sidebar" class="fixed left-0 top-0 h-full w-64 bg-white border-r border-gray-200 z-50 transform -translate-x-full transition-transform duration-300 md:hidden">
            <div class="px-6 py-6 border-b border-gray-200 flex items-center justify-between">
                <x-fri-logo size="sm" />
                <button id="close-mobile-menu" class="p-2 rounded-lg hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <nav class="px-4 py-6 space-y-1">
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-green-600 text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }} transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span>Dashboard Admin</span>
                    </a>
                    <a href="{{ route('admin.verifikasi-pengajuan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.verifikasi-pengajuan') || request()->routeIs('admin.submission-detail') ? 'bg-green-600 text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }} transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <span>Verifikasi Pengajuan</span>
                    </a>
                    <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.users*') ? 'bg-green-600 text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }} transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span>Manage Users</span>
                    </a>
                    <a href="{{ route('admin.periodes') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.periodes*') ? 'bg-green-600 text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }} transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Manajemen Periode</span>
                    </a>
                    <a href="{{ route('admin.yudisium-sidings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.yudisium-sidings*') ? 'bg-green-600 text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }} transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Sidang Yudisium</span>
                    </a>
                    <a href="{{ route('admin.dosens') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.dosens*') ? 'bg-green-600 text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }} transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>Database Dosen</span>
                    </a>
                @else
                    <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('student.dashboard') ? 'bg-green-600 text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }} transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('student.pengajuan-yudisium') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('student.pengajuan-yudisium') ? 'bg-green-600 text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }} transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                        </svg>
                        <span>Pengajuan Yudisium</span>
                    </a>
                @endif

                <div class="mt-8 pt-8 border-t border-gray-200">
                    <div class="text-xs text-gray-500 uppercase font-semibold mb-2 px-3">Support</div>
                    <a href="{{ route('help.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('help.index') ? 'bg-green-600 text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }} transition text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Help & Support
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 md:ml-64 ml-0">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <span>&copy; {{ date('Y') }} SIYU - Sistem Informasi Yudisium. All rights reserved.</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <span>Fakultas Rekayasa Industri - Telkom University</span>
                    </div>
                </div>
            </div>
        </footer>
        
        <script>
            // Mobile menu toggle
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const closeBtn = document.getElementById('close-mobile-menu');
            const mobileSidebar = document.getElementById('mobile-sidebar');
            const mobileOverlay = document.getElementById('mobile-sidebar-overlay');

            function openMobileMenu() {
                mobileSidebar.classList.remove('-translate-x-full');
                mobileOverlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeMobileMenu() {
                mobileSidebar.classList.add('-translate-x-full');
                mobileOverlay.classList.add('hidden');
                document.body.style.overflow = '';
            }

            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', openMobileMenu);
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', closeMobileMenu);
            }

            if (mobileOverlay) {
                mobileOverlay.addEventListener('click', closeMobileMenu);
            }

            // Notifications function for Alpine.js
            function notifications() {
                return {
                    showModal: false,
                    notificationList: [],
                    unreadCount: 0,

                    openModal() {
                        this.showModal = true;
                        this.fetchNotifications();
                    },

                    closeModal() {
                        this.showModal = false;
                    },

                    async fetchNotifications() {
                        try {
                            const response = await fetch('{{ route('student.notifications') }}');
                            const data = await response.json();
                            this.notificationList = data.notifications;
                            this.unreadCount = data.unreadCount;
                        } catch (error) {
                            console.error('Error fetching notifications:', error);
                        }
                    },

                    async markAsRead(notif) {
                        if (notif.is_read) return;

                        try {
                            await fetch(`/student/notifications/${notif.id}/read`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                }
                            });
                            notif.is_read = true;
                            this.unreadCount = Math.max(0, this.unreadCount - 1);
                        } catch (error) {
                            console.error('Error marking notification as read:', error);
                        }
                    },

                    async markAllAsRead() {
                        try {
                            await fetch('{{ route('student.mark-all-notifications-read') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                }
                            });
                            this.notificationList.forEach(n => n.is_read = true);
                            this.unreadCount = 0;
                        } catch (error) {
                            console.error('Error marking all notifications as read:', error);
                        }
                    },

                    async deleteAllNotifications() {
                        if (!confirm('Apakah Anda yakin ingin menghapus semua notifikasi?')) {
                            return;
                        }

                        try {
                            await fetch('{{ route('student.delete-all-notifications') }}', {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                }
                            });
                            this.notificationList = [];
                            this.unreadCount = 0;
                            this.closeModal();
                        } catch (error) {
                            console.error('Error deleting all notifications:', error);
                        }
                    },

                    formatDate(dateString) {
                        const date = new Date(dateString);
                        const now = new Date();
                        const diffMs = now - date;
                        const diffMins = Math.floor(diffMs / 60000);
                        const diffHours = Math.floor(diffMs / 3600000);
                        const diffDays = Math.floor(diffMs / 86400000);

                        if (diffMins < 1) return 'Baru saja';
                        if (diffMins < 60) return `${diffMins} menit yang lalu`;
                        if (diffHours < 24) return `${diffHours} jam yang lalu`;
                        if (diffDays < 7) return `${diffDays} hari yang lalu`;
                        
                        return date.toLocaleDateString('id-ID', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        });
                    },

                    init() {
                        this.fetchNotifications();
                        // Refresh notifications every 30 seconds
                        setInterval(() => this.fetchNotifications(), 30000);
                    }
                }
            }
        </script>
    </body>
</html>

