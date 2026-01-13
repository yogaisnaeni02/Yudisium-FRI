<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIYU - Sistem Informasi Yudisium | Fakultas Rekayasa Industri</title>
    <meta name="description" content="Sistem Informasi Yudisium Terintegrasi untuk Fakultas Rekayasa Industri Telkom University">

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ file_exists(public_path('favicon.ico')) ? asset('favicon.ico') : asset('images/logo/fri-icon.png') }}">
        <link rel="apple-touch-icon" href="{{ file_exists(public_path('images/logo/fri-icon.png')) ? asset('images/logo/fri-icon.png') : '' }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

            @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-3">
                    <x-fri-icon size="md" class="flex-shrink-0" />
                    <div>
                        <div class="text-lg font-bold text-gray-900">SIYU</div>
                        <div class="text-xs text-gray-600">Sistem Informasi Yudisium</div>
                    </div>
                </div>
                <div class="hidden md:flex items-center gap-6">
                    <a href="#features" class="text-gray-700 hover:text-green-700 font-medium transition">Fitur</a>
                    <a href="#about" class="text-gray-700 hover:text-green-700 font-medium transition">Tentang</a>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                                Dashboard
                            </a>
        @else
                            <a href="{{ route('login') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                                Masuk
                            </a>
                        @endauth
        @endif
                </div>
                <div class="md:hidden">
                    <button id="mobile-menu-btn" class="text-gray-700 hover:text-green-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t">
            <div class="px-4 py-4 space-y-3">
                <a href="#features" class="block text-gray-700 hover:text-green-700 font-medium">Fitur</a>
                <a href="#about" class="block text-gray-700 hover:text-green-700 font-medium">Tentang</a>
            @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="block bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold text-center">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="block bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold text-center">Masuk</a>
                        @endauth
                @endif
            </div>
        </div>
                </nav>

    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-green-50 via-white to-green-50 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-semibold mb-6">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7m0 0l-3-3m3 3l3-3"></path>
                        </svg>
                        Fakultas Rekayasa Industri
                    </div>
                    <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                        Sistem Informasi
                        <span class="text-green-700">Yudisium</span>
                        Terintegrasi
                    </h1>
                    <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                        Platform digital terpadu untuk pengajuan dan verifikasi yudisium mahasiswa 
                        Fakultas Rekayasa Industri Telkom University. Proses yang lebih cepat, 
                        efisien, dan transparan.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-lg font-semibold text-lg transition shadow-lg hover:shadow-xl">
                                Buka Dashboard
                        </a>
                    @else
                            <a href="{{ route('login') }}" class="bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-lg font-semibold text-lg transition shadow-lg hover:shadow-xl">
                                Masuk ke Akun
                            </a>
                    @endauth
                    </div>
                </div>
                <div class="relative">
                    <div class="absolute inset-0 bg-green-200 rounded-3xl transform rotate-6 opacity-20"></div>
                    <div class="relative bg-white rounded-3xl shadow-2xl p-8 border border-green-100">
                        <div class="space-y-4">
                            <div class="flex items-center gap-4 p-4 bg-green-50 rounded-lg">
                                <x-fri-icon size="md" />
                                <div>
                                    <div class="font-semibold text-gray-900">Dashboard Mahasiswa</div>
                                    <div class="text-sm text-gray-600">Pantau status pengajuan yudisium</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 p-4 bg-blue-50 rounded-lg">
                                <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center text-white">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">Upload Dokumen</div>
                                    <div class="text-sm text-gray-600">Unggah berkas yudisium dengan mudah</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 p-4 bg-yellow-50 rounded-lg">
                                <div class="w-12 h-12 bg-yellow-600 rounded-lg flex items-center justify-center text-white">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">Verifikasi Admin</div>
                                    <div class="text-sm text-gray-600">Proses verifikasi yang transparan</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Fitur Unggulan</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Platform yang dirancang khusus untuk memudahkan proses yudisium
                </p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-gradient-to-br from-green-50 to-white p-8 rounded-2xl border border-green-100 hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-green-600 rounded-xl flex items-center justify-center text-white mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Pengajuan Digital</h3>
                    <p class="text-gray-600">
                        Ajukan yudisium secara online dengan interface yang user-friendly. 
                        Upload dokumen dengan mudah dan pantau status pengajuan secara real-time.
                    </p>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-white p-8 rounded-2xl border border-blue-100 hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-blue-600 rounded-xl flex items-center justify-center text-white mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Verifikasi Cepat</h3>
                    <p class="text-gray-600">
                        Admin dapat memverifikasi dan menyetujui dokumen dengan cepat. 
                        Sistem notifikasi memberikan update real-time kepada mahasiswa.
                    </p>
                </div>
                <div class="bg-gradient-to-br from-yellow-50 to-white p-8 rounded-2xl border border-yellow-100 hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-yellow-600 rounded-xl flex items-center justify-center text-white mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Tracking Progress</h3>
                    <p class="text-gray-600">
                        Pantau progress pengajuan yudisium dengan detail. Lihat status setiap 
                        dokumen dan terima feedback langsung dari admin.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-gradient-to-br from-green-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-6">Tentang SIYU</h2>
                    <p class="text-lg text-gray-600 mb-4 leading-relaxed">
                        SIYU (Sistem Informasi Yudisium) adalah platform digital terintegrasi 
                        yang dikembangkan untuk Fakultas Rekayasa Industri Telkom University. 
                        Sistem ini dirancang untuk menyederhanakan proses pengajuan dan verifikasi 
                        yudisium mahasiswa.
                    </p>
                    <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                        Dengan SIYU, proses yudisium menjadi lebih efisien, transparan, dan mudah 
                        diakses. Mahasiswa dapat mengunggah dokumen dengan mudah, sedangkan admin 
                        dapat melakukan verifikasi dengan cepat dan memberikan feedback secara langsung.
                    </p>
                    <div class="flex gap-4">
                        <div class="bg-green-600 text-white px-6 py-3 rounded-lg">
                            <div class="text-2xl font-bold">100+</div>
                            <div class="text-sm">Mahasiswa Terdaftar</div>
                        </div>
                        <div class="bg-green-600 text-white px-6 py-3 rounded-lg">
                            <div class="text-2xl font-bold">24/7</div>
                            <div class="text-sm">Akses Online</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-xl p-8 border border-green-100">
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600 font-bold flex-shrink-0">
                                1
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-2">Daftar Akun</h4>
                                <p class="text-gray-600">Buat akun dengan mudah menggunakan email mahasiswa</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600 font-bold flex-shrink-0">
                                2
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-2">Upload Dokumen</h4>
                                <p class="text-gray-600">Unggah semua dokumen yudisium yang diperlukan</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600 font-bold flex-shrink-0">
                                3
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-2">Submit Pengajuan</h4>
                                <p class="text-gray-600">Kirim pengajuan yudisium untuk diverifikasi admin</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600 font-bold flex-shrink-0">
                                4
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-2">Pantau Status</h4>
                                <p class="text-gray-600">Lihat progress verifikasi dan terima notifikasi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-green-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-white mb-4">Siap Memulai?</h2>
            <p class="text-xl text-green-100 mb-8 max-w-2xl mx-auto">
                Bergabung dengan mahasiswa yang telah menggunakan SIYU untuk pengajuan yudisium mereka
            </p>
            @auth
                <a href="{{ url('/dashboard') }}" class="inline-block bg-white text-green-700 hover:bg-green-50 px-8 py-4 rounded-lg font-semibold text-lg transition shadow-lg">
                    Buka Dashboard
                        </a>
                    @else
                <a href="{{ route('login') }}" class="inline-block bg-white text-green-700 hover:bg-green-50 px-8 py-4 rounded-lg font-semibold text-lg transition shadow-lg">
                    Masuk ke Akun
                </a>
            @endauth
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8 mb-8">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <x-fri-icon size="sm" />
                        <div>
                            <div class="font-bold">SIYU</div>
                            <div class="text-xs text-gray-400">Sistem Informasi Yudisium</div>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm">
                        Platform digital terintegrasi untuk pengajuan dan verifikasi yudisium 
                        Fakultas Rekayasa Industri Telkom University.
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Tautan Cepat</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#features" class="hover:text-white transition">Fitur</a></li>
                        <li><a href="#about" class="hover:text-white transition">Tentang</a></li>
                        @if (Route::has('login'))
                            <li><a href="{{ route('login') }}" class="hover:text-white transition">Masuk</a></li>
                        @endif
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Kontak</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li>Fakultas Rekayasa Industri</li>
                        <li>Telkom University</li>
                        <li>Bandung, Indonesia</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} SIYU - Sistem Informasi Yudisium. All rights reserved.</p>
                </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
    </body>
</html>
