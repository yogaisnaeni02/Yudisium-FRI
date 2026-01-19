@extends('layouts.dashboard')

@section('title', 'Detail Sidang Yudisium')
@section('page-title', 'Detail Sidang Yudisium')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-green-600 hover:text-green-700">Dashboard</a>
    <span class="mx-2 text-gray-400">/</span>
    <a href="{{ route('admin.yudisium-sidings') }}" class="text-green-600 hover:text-green-700">Sidang Yudisium</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Detail</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Action Buttons -->
    <div class="flex items-center justify-between">
        <x-button variant="ghost" href="{{ route('admin.yudisium-sidings') }}" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>'>
            Kembali
        </x-button>
        <div class="flex gap-2">
            <x-button variant="success" href="{{ route('admin.yudisium-sidings.export-pdf', $siding) }}" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>'>
                Export PDF
            </x-button>
            <x-button variant="primary" href="{{ route('admin.yudisium-sidings.edit', $siding) }}" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>'>
                Edit
            </x-button>
        </div>
    </div>

    <!-- Main Document Card -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-8 py-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Sidang Yudisium</h1>
                    <p class="text-green-100">Fakultas Rekayasa Industri - Telkom University</p>
                    <p class="text-green-100 mt-1">
                        {{ $siding->periode->nama ?? 'Periode Tidak Ditetapkan' }} - 
                        Tahun {{ $siding->tanggal_sidang ? $siding->tanggal_sidang->format('Y') : date('Y') }}
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-green-100">Status</div>
                    @if($siding->status_yudisium === 'lulus')
                        <span class="inline-flex items-center px-4 py-2 rounded-lg text-lg font-bold bg-white text-green-600 mt-1">
                            Lulus Yudisium
                        </span>
                    @elseif($siding->status_yudisium === 'tidak_lulus')
                        <span class="inline-flex items-center px-4 py-2 rounded-lg text-lg font-bold bg-white text-red-600 mt-1">
                            Tidak Lulus
                        </span>
                    @else
                        <span class="inline-flex items-center px-4 py-2 rounded-lg text-lg font-bold bg-white text-yellow-600 mt-1">
                            Pending
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-8 space-y-8">
            <!-- Student Information Section -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Informasi Mahasiswa</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2">
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Nama</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $siding->student->nama }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">NIM</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $siding->student->nim }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Program Studi</label>
                                <p class="text-lg text-gray-900">{{ $siding->student->prodi }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-center md:justify-end">
                        <div class="w-32 h-40 bg-gray-200 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300">
                            @if($siding->student->foto)
                                <img src="{{ Storage::url($siding->student->foto) }}" alt="Foto Mahasiswa" class="w-full h-full object-cover rounded-lg">
                            @else
                                <span class="text-gray-400 text-sm text-center px-2">Foto Mahasiswa</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Summary Section -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Ringkasan Akademik</h2>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                    <!-- Dosen Wali -->
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-2 block">Dosen Wali</label>
                        <div class="flex flex-col items-center">
                            @if($siding->dosen_wali_foto)
                                <img src="{{ Storage::url($siding->dosen_wali_foto) }}" alt="Dosen Wali" class="w-20 h-20 rounded-full object-cover mb-2 border-2 border-gray-300">
                            @else
                                <div class="w-20 h-20 rounded-full bg-gray-200 border-2 border-gray-300 flex items-center justify-center mb-2">
                                    <svg class="w-10 h-10 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @endif
                            <p class="text-sm font-medium text-gray-900 text-center">{{ $siding->dosen_wali_nama ?? '-' }}</p>
                            @if($siding->dosen_wali_nama)
                                @php
                                    $dosenWali = \App\Models\Dosen::where('nama_dosen', $siding->dosen_wali_nama)->first();
                                @endphp
                                @if($dosenWali)
                                    <p class="text-xs text-gray-500 text-center mt-1">NIP: {{ $dosenWali->nip }}</p>
                                @endif
                            @endif
                        </div>
                    </div>
                    <!-- IPK -->
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-2 block">IPK</label>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($siding->student->ipk ?? 0, 2) }}</p>
                    </div>
                    <!-- Total SKS -->
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-2 block">Total SKS</label>
                        <p class="text-2xl font-bold text-gray-900">{{ $siding->student->total_sks ?? '-' }}</p>
                    </div>
                    <!-- EPRT -->
                    <div>
                    <label class="text-sm font-medium text-gray-600 mb-2 block">Skor EPRT</label>
                    <p class="text-2xl font-bold text-gray-900">{{ $siding->student->skor_eprt ?? '-' }}</p>
                    </div>
                    <!-- TAK -->
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-2 block">Total TAK</label>
                        <p class="text-2xl font-bold text-gray-900">{{ $siding->student->tak ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Final Project Section -->
            @if($siding->judul_tugas_akhir)
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Tugas Akhir</h2>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Judul Tugas Akhir</label>
                        <p class="text-lg text-gray-900 mt-1">{{ $siding->judul_tugas_akhir }}</p>
                    </div>
                    @if($siding->jenis_tugas_akhir)
                    <div>
                        <label class="text-sm font-medium text-gray-600">Jenis Tugas Akhir</label>
                        <p class="text-lg text-gray-900 mt-1">{{ $siding->jenis_tugas_akhir }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Grades Section -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Nilai</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Pembimbing 1 -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center gap-4 mb-3">
                            @if($siding->pembimbing_1_foto)
                                <img src="{{ Storage::url($siding->pembimbing_1_foto) }}" alt="Pembimbing 1" class="w-16 h-16 rounded-full object-cover border-2 border-gray-300">
                            @else
                                <div class="w-16 h-16 rounded-full bg-gray-200 border-2 border-gray-300 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-600">Pembimbing 1</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $siding->pembimbing_1_nama ?? '-' }}</p>
                                @if($siding->pembimbing_1_nama)
                                    @php
                                        $pembimbing1 = \App\Models\Dosen::where('nama_dosen', $siding->pembimbing_1_nama)->first();
                                    @endphp
                                    @if($pembimbing1)
                                        <p class="text-xs text-gray-500 mt-1">NIP: {{ $pembimbing1->nip }}</p>
                                    @endif
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-green-600">{{ $siding->pembimbing_1_nilai ? number_format($siding->pembimbing_1_nilai, 2) : '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pembimbing 2 -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center gap-4 mb-3">
                            @if($siding->pembimbing_2_foto)
                                <img src="{{ Storage::url($siding->pembimbing_2_foto) }}" alt="Pembimbing 2" class="w-16 h-16 rounded-full object-cover border-2 border-gray-300">
                            @else
                                <div class="w-16 h-16 rounded-full bg-gray-200 border-2 border-gray-300 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-600">Pembimbing 2</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $siding->pembimbing_2_nama ?? '-' }}</p>
                                @if($siding->pembimbing_2_nama)
                                    @php
                                        $pembimbing2 = \App\Models\Dosen::where('nama_dosen', $siding->pembimbing_2_nama)->first();
                                    @endphp
                                    @if($pembimbing2)
                                        <p class="text-xs text-gray-500 mt-1">NIP: {{ $pembimbing2->nip }}</p>
                                    @endif
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-green-600">{{ $siding->pembimbing_2_nilai ? number_format($siding->pembimbing_2_nilai, 2) : '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Penguji Ketua -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center gap-4 mb-3">
                            @if($siding->penguji_ketua_foto)
                                <img src="{{ Storage::url($siding->penguji_ketua_foto) }}" alt="Penguji Ketua" class="w-16 h-16 rounded-full object-cover border-2 border-gray-300">
                            @else
                                <div class="w-16 h-16 rounded-full bg-gray-200 border-2 border-gray-300 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-600">Penguji Ketua</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $siding->penguji_ketua_nama ?? '-' }}</p>
                                @if($siding->penguji_ketua_nama)
                                    @php
                                        $pengujiKetua = \App\Models\Dosen::where('nama_dosen', $siding->penguji_ketua_nama)->first();
                                    @endphp
                                    @if($pengujiKetua)
                                        <p class="text-xs text-gray-500 mt-1">NIP: {{ $pengujiKetua->nip }}</p>
                                    @endif
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-green-600">{{ $siding->penguji_ketua_nilai ? number_format($siding->penguji_ketua_nilai, 2) : '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Penguji Anggota -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center gap-4 mb-3">
                            @if($siding->penguji_anggota_foto)
                                <img src="{{ Storage::url($siding->penguji_anggota_foto) }}" alt="Penguji Anggota" class="w-16 h-16 rounded-full object-cover border-2 border-gray-300">
                            @else
                                <div class="w-16 h-16 rounded-full bg-gray-200 border-2 border-gray-300 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-600">Penguji Anggota</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $siding->penguji_anggota_nama ?? '-' }}</p>
                                @if($siding->penguji_anggota_nama)
                                    @php
                                        $pengujiAnggota = \App\Models\Dosen::where('nama_dosen', $siding->penguji_anggota_nama)->first();
                                    @endphp
                                    @if($pengujiAnggota)
                                        <p class="text-xs text-gray-500 mt-1">NIP: {{ $pengujiAnggota->nip }}</p>
                                    @endif
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-green-600">{{ $siding->penguji_anggota_nilai ? number_format($siding->penguji_anggota_nilai, 2) : '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Overall Grade -->
                <div class="mt-6 bg-green-50 rounded-lg p-6 border-2 border-green-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Nilai Total</p>
                            <p class="text-3xl font-bold text-green-600">
                                {{ $siding->nilai_total ? number_format($siding->nilai_total, 2) : '-' }}
                                @if($siding->nilai_huruf)
                                    <span class="text-2xl text-gray-700">({{ $siding->nilai_huruf }})</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Yudisium Result Section -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Predikat Yudisium</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-2 block">Predikat</label>
                        <p class="text-xl font-bold text-gray-900">{{ $siding->predikat_yudisium ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-2 block">Status Cumlaude/Summa Cumlaude</label>
                        <p class="text-xl font-bold text-gray-900">
                            @if($siding->status_cumlaude === 'cumlaude')
                                Cumlaude
                            @elseif($siding->status_cumlaude === 'summa_cumlaude')
                                Summa Cumlaude
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    @if($siding->pemenuhan_jurnal)
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-600 mb-2 block">Pemenuhan Jurnal/Publikasi/Lomba/dsb</label>
                        <p class="text-gray-900">{{ $siding->pemenuhan_jurnal }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Status and Notes -->
            <div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-2 block">Status Yudisium</label>
                        <p class="text-lg font-semibold text-gray-900">
                            @if($siding->status_yudisium === 'lulus')
                                Lulus Yudisium
                            @elseif($siding->status_yudisium === 'tidak_lulus')
                                Tidak Lulus
                            @else
                                Pending
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600 mb-2 block">Tanggal</label>
                        <p class="text-lg text-gray-900">
                            @if($siding->tanggal_sidang)
                                {{ $siding->tanggal_sidang->format('Y-m-d H:i') }} WIB
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>
                @if($siding->catatan)
                <div>
                    <label class="text-sm font-medium text-gray-600 mb-2 block">Catatan</label>
                    <p class="text-gray-900 bg-gray-50 p-4 rounded-lg">{{ $siding->catatan }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
