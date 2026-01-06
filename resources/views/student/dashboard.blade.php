@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 flex">
    <!-- Left Sidebar -->
    <aside class="w-64 bg-gray-50 border-r border-gray-200 hidden md:block">
        <div class="px-6 py-6 border-b border-gray-200 flex items-center gap-3">
            <div class="w-10 h-10 bg-green-700 rounded flex items-center justify-center text-white font-bold">TI</div>
            <div>
                <div class="text-sm font-semibold">Fakultas Rekayasa Industri</div>
                <div class="text-xs text-gray-500">Telkom University</div>
            </div>
        </div>
        <nav class="px-4 py-6 space-y-2">
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded bg-green-600 text-white font-semibold">
                <span class="w-5">🏠</span>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('student.pengajuan-yudisium') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-100">
                <span class="w-5">🗂️</span>
                <span>Pengajuan Yudisium</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-100">
                <span class="w-5">📄</span>
                <span>Status Yudisium</span>
            </a>

            <div class="mt-8 text-xs text-gray-500 uppercase">Support</div>
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-100 text-sm text-gray-700">
                <span>❓</span> Help & Support
            </a>
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-100 text-sm text-gray-700">
                <span>⚙️</span> Settings
            </a>
        </nav>
    </aside>

    <!-- Main content area -->
    <main class="flex-1">
        <!-- Top header -->
        <header class="bg-green-700 text-white px-6 py-4 flex items-center justify-between">
            <h2 class="text-xl font-semibold">Dashboard</h2>
            <div class="flex items-center gap-4">
                <div class="text-sm text-white/80">Welcome, {{ $student->nama ?? $student->user->name }}</div>
                <div class="w-10 h-10 bg-white rounded-full overflow-hidden">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($student->nama ?? $student->user->name) }}&background=ffffff&color=007f00" alt="avatar" class="w-full h-full object-cover">
                </div>
            </div>
        </header>

        <div class="p-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Profile Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center text-3xl">{{ strtoupper(substr($student->nama ?? $student->user->name,0,1)) }}</div>
                        <div>
                            <h3 class="text-lg font-bold">{{ $student->nama ?? $student->user->name }}</h3>
                            <div class="text-sm text-gray-600">NIM : {{ $student->nim }}</div>
                            <div class="text-sm text-gray-600">Program Studi: {{ $student->program_studi ?? 'S1 Sistem Informasi' }}</div>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-4 text-sm text-gray-700">
                        <div>
                            <div class="text-xs text-gray-500">IPK</div>
                            <div class="font-semibold">{{ $student->ipk }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Total SKS</div>
                            <div class="font-semibold">{{ $student->total_sks }}</div>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
                    <h4 class="font-semibold text-gray-800">Timeline Yudisium</h4>
                    <ul class="mt-4 space-y-3 text-sm text-gray-700">
                        <li class="flex items-start gap-3"><div class="text-green-600 mt-1">✔️</div><div>Memuat Profile</div></li>
                        <li class="flex items-start gap-3"><div class="text-green-600 mt-1">✔️</div><div>Cek Status Mahasiswa</div></li>
                        <li class="flex items-start gap-3"><div class="text-yellow-500 mt-1">⏳</div><div>Upload Berkas Yudisium</div></li>
                        <li class="flex items-start gap-3"><div class="text-gray-400 mt-1">○</div><div>Verifikasi Berkas Yudisium</div></li>
                        <li class="flex items-start gap-3"><div class="text-gray-400 mt-1">○</div><div>Revisi Berkas</div></li>
                        <li class="flex items-start gap-3"><div class="text-gray-400 mt-1">○</div><div>Disetujui</div></li>
                    </ul>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
                <!-- Progress Dokumen -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h5 class="font-semibold text-gray-800">Progress Dokumen</h5>
                    <div class="mt-4">
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-green-600 h-3 rounded-full" style="width: {{ $progress }}%"></div>
                        </div>
                        <div class="text-sm text-gray-600 mt-2">{{ $documents->where('status','approved')->count() }} Berkas Diterima • {{ $documents->where('status','rejected')->count() }} Ditolak • {{ $documents->whereNull('file_path')->count() }} Kosong</div>
                    </div>
                </div>

                <!-- Nilai dan Keterangan -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h5 class="font-semibold text-gray-800">Nilai dan Keterangan</h5>
                    <div class="mt-4 grid grid-cols-2 gap-4 text-sm text-gray-700">
                        <div>
                            <div class="text-xs text-gray-500">Nilai Tugas Akhir</div>
                            <div class="font-semibold">A</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Poin TAK</div>
                            <div class="font-semibold">63</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">IPK</div>
                            <div class="font-semibold">{{ $student->ipk }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Cumlaude</div>
                            <div class="font-semibold">-</div>
                        </div>
                    </div>
                </div>

                <!-- Berita Terbaru (carousel-like) -->
                <div class="lg:col-span-3 bg-white rounded-lg shadow p-6 mt-6">
                    <h5 class="font-semibold text-gray-800 mb-4">Berita Terbaru</h5>
                    <div class="overflow-x-auto">
                        <div class="flex gap-4">
                            @for($i=0;$i<4;$i++)
                                <div class="w-64 bg-gray-50 rounded-lg p-3 flex-shrink-0">
                                    <div class="h-32 bg-gray-200 rounded mb-3"></div>
                                    <div class="text-sm font-semibold">Yudisium Fakultas Rekayasa Industri</div>
                                    <div class="text-xs text-gray-500 mt-2">Yudisium Gelombang I akan dilaksanakan pada tanggal 3 - 7 Juli 2026</div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
