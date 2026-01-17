@extends('layouts.dashboard')

@section('title', 'Edit Periode')
@section('page-title', 'Edit Periode Yudisium')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-green-600 hover:text-green-700">Dashboard</a>
    <span class="mx-2 text-gray-400">/</span>
    <a href="{{ route('admin.periodes') }}" class="text-green-600 hover:text-green-700">Manajemen Periode</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Edit Periode</span>
@endsection

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit Periode</h2>
        
        <form action="{{ route('admin.periodes.update', $periode) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Nama Periode -->
            <div>
                <label for="nama" class="block text-sm font-semibold text-gray-700 mb-2">Nama Periode *</label>
                <input type="text" 
                       id="nama" 
                       name="nama" 
                       value="{{ old('nama', $periode->nama) }}"
                       placeholder="e.g., Periode Yudisium Tahun 2026 - Genap"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('nama') border-red-500 @enderror"
                       required>
                @error('nama')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tanggal Mulai -->
            <div>
                <label for="tanggal_mulai" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai *</label>
                <input type="date" 
                       id="tanggal_mulai" 
                       name="tanggal_mulai" 
                       value="{{ old('tanggal_mulai', $periode->tanggal_mulai->format('Y-m-d')) }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('tanggal_mulai') border-red-500 @enderror"
                       required>
                @error('tanggal_mulai')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tanggal Selesai -->
            <div>
                <label for="tanggal_selesai" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Selesai *</label>
                <input type="date" 
                       id="tanggal_selesai" 
                       name="tanggal_selesai" 
                       value="{{ old('tanggal_selesai', $periode->tanggal_selesai->format('Y-m-d')) }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('tanggal_selesai') border-red-500 @enderror"
                       required>
                @error('tanggal_selesai')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status *</label>
                <select id="status" 
                        name="status"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('status') border-red-500 @enderror"
                        required>
                    <option value="">-- Pilih Status --</option>
                    <option value="active" {{ old('status', $periode->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ old('status', $periode->status) === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
                @error('status')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-4">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-6 rounded-lg transition shadow-sm hover:shadow flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.periodes') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2.5 px-6 rounded-lg transition shadow-sm hover:shadow flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
