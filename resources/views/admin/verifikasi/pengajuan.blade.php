@extends('layouts.dashboard')

@section('title', 'Verifikasi Pengajuan')
@section('page-title', 'Verifikasi Pengajuan Yudisium')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-green-600 hover:text-green-700">Dashboard</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Verifikasi Pengajuan</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- View Toggle & Search Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
            <!-- View Toggle Buttons -->
            <div class="flex gap-3">
                <a href="{{ route('admin.verifikasi-pengajuan', array_merge(request()->query(), ['view' => 'active'])) }}" 
                   class="px-6 py-2.5 rounded-lg font-semibold transition-all shadow-sm hover:shadow-md {{ $viewType === 'active' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Pengajuan Yudisium
                </a>
                <a href="{{ route('admin.verifikasi-pengajuan', array_merge(request()->query(), ['view' => 'completed'])) }}" 
                   class="px-6 py-2.5 rounded-lg font-semibold transition-all shadow-sm hover:shadow-md {{ $viewType === 'completed' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Sudah Selesai
                </a>
            </div>

            <!-- Export Button -->
            <div class="flex justify-end">
                <x-button variant="success" href="{{ route('admin.verifikasi-pengajuan.export', request()->query()) }}" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>'>
                    Export CSV
                </x-button>
            </div>
        </div>

        <!-- Search and Sort Form -->
        <form method="GET" action="{{ route('admin.verifikasi-pengajuan') }}" class="flex flex-col md:flex-row gap-3">
            <input type="hidden" name="view" value="{{ $viewType }}">
            
            <!-- Search Bar -->
            <div class="flex-1">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}"
                       placeholder="Cari berdasarkan NIM atau Nama..." 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            
            <!-- Sort Dropdown -->
            <div class="w-full md:w-48">
                <select name="sort" 
                        onchange="this.form.submit()"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white">
                    <option value="asc" {{ $sort === 'asc' ? 'selected' : '' }}>Terlama → Terbaru</option>
                    <option value="desc" {{ $sort === 'desc' ? 'selected' : '' }}>Terbaru → Terlama</option>
                </select>
            </div>
            
            <!-- Search Button -->
            <x-button variant="primary" type="submit" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>'>
                Cari
            </x-button>
            
            <!-- Reset Button -->
            @if($search)
            <x-button variant="secondary" href="{{ route('admin.verifikasi-pengajuan', ['view' => $viewType, 'sort' => $sort]) }}" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'>
                Reset
            </x-button>
            @endif
        </form>
    </div>

    <!-- Submissions Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-green-700">
            <h3 class="text-lg font-semibold text-white">
                {{ $viewType === 'completed' ? 'Daftar Pengajuan Yang Sudah Selesai' : 'Daftar Pengajuan Yudisium' }}
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">NIM</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nama Mahasiswa</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Progress</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tanggal Submit</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($submissions as $submission)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $submission->student->nim }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $submission->student->nama ?? $submission->student->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $submission->periode ? $submission->periode->nama : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
                                    @switch($submission->status)
                                        @case('draft')
                                            style="background-color: #f3f4f6; color: #4b5563;"
                                            @break
                                        @case('submitted')
                                            style="background-color: #fef3c7; color: #92400e;"
                                            @break
                                        @case('under_review')
                                            style="background-color: #e0e7ff; color: #3730a3;"
                                            @break
                                        @case('approved')
                                            style="background-color: #dcfce7; color: #166534;"
                                            @break
                                        @case('rejected')
                                            style="background-color: #fee2e2; color: #991b1b;"
                                            @break
                                    @endswitch
                                >
                                    {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-20 bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full transition-all" style="width: {{ $submission->getProgressPercentage() }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-600">{{ $submission->getProgressPercentage() }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $submission->submitted_at ? $submission->submitted_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <x-button variant="info" size="sm" href="{{ route('admin.submission-detail', $submission) }}" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>'>
                                        Detail
                                    </x-button>
                                    @if($viewType === 'completed')
                                        @php
                                            $yudisiumSiding = \App\Models\YudisiumSiding::where('student_id', $submission->student_id)
                                                ->where('periode_id', $submission->periode_id)
                                                ->first();
                                        @endphp
                                        @if($yudisiumSiding)
                                            <x-button variant="primary" size="sm" href="{{ route('admin.yudisium-sidings.edit', $yudisiumSiding) }}" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>'>
                                                Edit Sidang
                                            </x-button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="font-medium">Tidak ada pengajuan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($submissions->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $submissions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection