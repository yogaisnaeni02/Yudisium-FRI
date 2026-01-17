@extends('layouts.dashboard')

@section('title', 'Manajemen Periode')
@section('page-title', 'Manajemen Periode Yudisium')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-green-600 hover:text-green-700">Dashboard</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Manajemen Periode</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Periode Yudisium</h2>
            <p class="text-gray-600 mt-1">Kelola periode pengajuan dan verifikasi yudisium</p>
        </div>
        <x-button variant="primary" href="{{ route('admin.periodes.create') }}" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>'>
            Tambah Periode
        </x-button>
    </div>

    <!-- Periodes Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-green-700">
            <h3 class="text-lg font-semibold text-white">Daftar Periode</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nama Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tanggal Mulai</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tanggal Selesai</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($periodes as $periode)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $periode->nama }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $periode->tanggal_mulai->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $periode->tanggal_selesai->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
                                    style="{{ $periode->status === 'active' ? 'background-color: #dcfce7; color: #166534;' : 'background-color: #fee2e2; color: #991b1b;' }}">
                                    {{ ucfirst($periode->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <x-button variant="info" size="sm" href="{{ route('admin.periodes.edit', $periode) }}" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>'>
                                        Edit
                                    </x-button>
                                    <form action="{{ route('admin.periodes.delete', $periode) }}" method="POST" class="inline" onsubmit="return confirm('Apakah anda yakin ingin menghapus periode ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <x-button variant="danger" size="sm" type="submit" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>'>
                                            Hapus
                                        </x-button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="font-medium">Belum ada periode</p>
                                    <p class="text-sm">Mulai dengan membuat periode baru</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($periodes->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $periodes->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
