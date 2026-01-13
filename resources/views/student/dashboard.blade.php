@extends('layouts.dashboard')

@section('title', 'Dashboard Mahasiswa')
@section('page-title', 'Dashboard')

@php
    $studentName = $student->nama ?? $student->user->name;
@endphp

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-xl shadow-lg p-6 text-white">
        <h1 class="text-2xl font-bold mb-2">Selamat Datang, {{ Auth::user()->name }}!</h1>
        <p class="text-green-100">Dashboard Sistem Informasi Yudisium - Fakultas Rekayasa Industri</p>
    </div>
    
    <!-- Main Grid: 3 Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Column 1: Profile Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-green-600 to-green-700 flex items-center justify-center text-3xl text-white font-bold shadow-lg">
                    {{ strtoupper(substr($studentName, 0, 1)) }}
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $studentName }}</h3>
                    <div class="text-sm text-gray-600">NIM: {{ $student->nim }}</div>
                    <div class="text-sm text-gray-600">Program Studi: {{ $student->program_studi ?? 'S1 Sistem Informasi' }}</div>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-4 text-sm">
                <div class="p-3 bg-gray-50 rounded-lg">
                    <div class="text-xs text-gray-500 mb-1">IPK</div>
                    <div class="font-bold text-gray-900 text-lg">{{ $student->ipk }}</div>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <div class="text-xs text-gray-500 mb-1">Total SKS</div>
                    <div class="font-bold text-gray-900 text-lg">{{ $student->total_sks }}</div>
                </div>
            </div>
        </div>

        <!-- Column 2: Timeline Yudisium -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
            <h4 class="font-semibold text-gray-800 mb-4 text-lg">Timeline Yudisium</h4>
            <ul class="space-y-3 text-sm text-gray-700">
                <!-- Item 1: Memuat Profile -->
                <li class="flex items-start gap-3">
                    <div class="text-green-600 mt-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-medium">Memuat Profile</div>
                        <div class="text-xs text-gray-500">Profil mahasiswa berhasil dimuat</div>
                    </div>
                </li>

                <!-- Item 2: Cek Status -->
                <li class="flex items-start gap-3">
                    <div class="text-green-600 mt-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-medium">Cek Status</div>
                        <div class="text-xs text-gray-500">Status kelulusan telah diverifikasi</div>
                    </div>
                </li>

                <!-- Item 3: Upload Berkas -->
                <li class="flex items-start gap-3">
                    <div class="text-yellow-500 mt-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-medium">Upload Berkas</div>
                        <div class="text-xs text-gray-500">Unggah semua dokumen yang diperlukan</div>
                    </div>
                </li>

                <!-- Item 4: Verifikasi -->
                <li class="flex items-start gap-3">
                    <div class="text-gray-400 mt-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-medium text-gray-400">Verifikasi</div>
                        <div class="text-xs text-gray-400">Tunggu verifikasi dari admin</div>
                    </div>
                </li>

                <!-- Item 5: Revisi -->
                <li class="flex items-start gap-3">
                    <div class="text-gray-400 mt-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-medium text-gray-400">Revisi</div>
                        <div class="text-xs text-gray-400">Jika diperlukan revisi</div>
                    </div>
                </li>

                <!-- Item 6: Disetujui -->
                <li class="flex items-start gap-3">
                    <div class="text-gray-400 mt-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-medium text-gray-400">Disetujui</div>
                        <div class="text-xs text-gray-400">Pengajuan yudisium disetujui</div>
                    </div>
                </li>

                <!-- Item 7: Selesai -->
                <li class="flex items-start gap-3">
                    <div class="text-gray-400 mt-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-medium text-gray-400">Selesai</div>
                        <div class="text-xs text-gray-400">Proses yudisium selesai</div>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Column 3: Right Panel (Progress + Quick Actions) -->
        <div class="space-y-6">
            <!-- Progress Dokumen -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                <h5 class="font-semibold text-gray-800 mb-4">Progress Dokumen</h5>
                <div class="mb-4">
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="bg-gradient-to-r from-green-500 to-green-600 h-4 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                    </div>
                    <div class="mt-2 text-center">
                        <span class="text-2xl font-bold text-gray-900">{{ $progress }}%</span>
                    </div>
                </div>
                <div class="text-sm text-gray-600 space-y-1">
                    <div class="flex justify-between items-center">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Diterima:
                        </span>
                        <span class="font-semibold text-green-600">{{ $documents->where('status','approved')->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Ditolak:
                        </span>
                        <span class="font-semibold text-red-600">{{ $documents->where('status','rejected')->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Kosong:
                        </span>
                        <span class="font-semibold text-gray-600">{{ $documents->whereNull('file_path')->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-gradient-to-br from-green-50 to-white rounded-xl shadow-sm border border-green-200 p-6 hover:shadow-md transition">
                <h5 class="font-semibold text-gray-800 mb-4">Quick Actions</h5>
                <div class="space-y-3">
                    <a href="{{ route('student.pengajuan-yudisium') }}" class="block w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition text-center flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Upload Dokumen
                    </a>
                    @if($submission->status === 'draft' && $documents->count() > 0)
                    <form action="{{ route('student.submit-application') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Submit Pengajuan
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>




    <!-- Informasi Yudisium Terbaru -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-6">
            <h5 class="font-semibold text-gray-800 text-lg">Informasi Yudisium Terbaru</h5>
            <a href="{{ route('student.articles') }}" class="text-sm text-green-600 hover:text-green-700 font-medium flex items-center gap-1">
                Lihat Semua
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            @forelse($latestArticles as $article)
                <a href="{{ route('student.article-detail', $article) }}" class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 group flex flex-col">
                    @if($article->image)
                        <div class="h-40 bg-gray-200 overflow-hidden">
                            <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                        </div>
                    @else
                        <div class="h-40 bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center">
                            <svg class="w-12 h-12 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    @endif
                    <div class="p-4 flex-1 flex flex-col">
                        <div class="text-xs text-gray-500 mb-2">{{ $article->published_at->format('d M Y') }}</div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-2 group-hover:text-green-600 transition line-clamp-2 flex-shrink-0">{{ $article->title }}</h3>
                        <p class="text-xs text-gray-600 line-clamp-3 flex-grow">{{ $article->excerpt ?? Str::limit(strip_tags($article->content), 100) }}</p>
                        <div class="mt-3 flex items-center text-green-600 text-xs font-medium">
                            Baca selengkapnya
                            <svg class="w-3 h-3 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-12 text-center text-gray-500">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="font-medium">Belum ada informasi yudisium</p>
                    <p class="text-sm mt-2 text-gray-400">Informasi akan muncul di sini setelah dipublikasikan oleh admin</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
