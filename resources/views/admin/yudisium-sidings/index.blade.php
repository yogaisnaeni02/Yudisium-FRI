@extends('layouts.dashboard')

@section('title', 'Sidang Yudisium')
@section('page-title', 'Manajemen Sidang Yudisium')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-green-600 hover:text-green-700">Dashboard</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Sidang Yudisium</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Sidang Yudisium</h2>
            <p class="text-gray-600 mt-1">Kelola data sidang yudisium mahasiswa yang sudah approved</p>
            <p class="text-sm text-gray-500 mt-1">Data otomatis masuk dari pengajuan yang sudah di-approve dan memiliki periode & predikat</p>
        </div>
        <div class="flex gap-3">
            <x-button variant="success" href="{{ route('admin.yudisium-sidings.export', request()->query()) }}" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>'>
                Export CSV
            </x-button>
            <x-button variant="primary" href="{{ route('admin.yudisium-sidings.create') }}" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>'>
                Tambah Sidang Yudisium
            </x-button>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('admin.yudisium-sidings') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Mahasiswa</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Nama atau NIM..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
                <select name="periode" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="">Semua Periode</option>
                    @foreach($periodes as $periode)
                        <option value="{{ $periode->id }}" {{ $periodeFilter == $periode->id ? 'selected' : '' }}>
                            {{ $periode->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <x-button variant="info" type="submit" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>'>
                    Filter
                </x-button>
                @if($search || $periodeFilter)
                    <x-button variant="secondary" href="{{ route('admin.yudisium-sidings') }}" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'>
                        Reset
                    </x-button>
                @endif
            </div>
        </form>
    </div>

    <!-- Sidings Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-green-700">
            <h3 class="text-lg font-semibold text-white">Daftar Sidang Yudisium</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Mahasiswa</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tanggal Sidang</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nilai</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Predikat</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($sidings as $siding)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $siding->student->nama }}</div>
                                        <div class="text-sm text-gray-500">{{ $siding->student->nim }}</div>
                                    </div>
                                    @if($siding->status_yudisium === 'pending' && !$siding->pembimbing_1_nama)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800" title="Data auto-create dari pengajuan yang approved">
                                            Auto
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $siding->periode->nama ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $siding->tanggal_sidang ? $siding->tanggal_sidang->format('d M Y, H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                @if($siding->nilai_total)
                                    {{ number_format($siding->nilai_total, 2) }} 
                                    @if($siding->nilai_huruf)
                                        <span class="text-gray-500">({{ $siding->nilai_huruf }})</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($siding->predikat_yudisium)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        {{ $siding->predikat_yudisium }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($siding->status_yudisium === 'lulus')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        Lulus
                                    </span>
                                @elseif($siding->status_yudisium === 'tidak_lulus')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        Tidak Lulus
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <x-button variant="info" size="sm" href="{{ route('admin.yudisium-sidings.show', $siding) }}" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>'>
                                        Detail
                                    </x-button>
                                    <x-button variant="primary" size="sm" href="{{ route('admin.yudisium-sidings.edit', $siding) }}" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>'>
                                        Edit
                                    </x-button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">Belum ada data sidang yudisium</p>
                                    <p class="text-sm">Mulai dengan menambahkan data sidang yudisium baru</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sidings->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $sidings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
