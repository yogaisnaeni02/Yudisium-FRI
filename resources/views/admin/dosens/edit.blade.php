@extends('layouts.dashboard')

@section('title', 'Edit Data Dosen')
@section('page-title', 'Edit Data Dosen')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-green-600 hover:text-green-700">Dashboard</a>
    <span class="mx-2 text-gray-400">/</span>
    <a href="{{ route('admin.dosens') }}" class="text-green-600 hover:text-green-700">Manajemen Dosen</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Edit Data Dosen</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Edit Data Dosen</h2>
            <p class="text-gray-600">Perbarui informasi data dosen</p>
        </div>

        <form action="{{ route('admin.dosens.update', $dosen) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Nama Dosen -->
            <div class="mb-6">
                <x-input-label for="nama_dosen" :value="__('Nama Dosen')" class="text-gray-700 font-semibold mb-2" />
                <x-text-input id="nama_dosen" 
                    class="block mt-1 w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg" 
                    type="text" 
                    name="nama_dosen" 
                    :value="old('nama_dosen', $dosen->nama_dosen)" 
                    required 
                    autofocus
                    placeholder="Masukkan nama lengkap dosen" />
                <x-input-error :messages="$errors->get('nama_dosen')" class="mt-2" />
            </div>

            <!-- Kode Dosen -->
            <div class="mb-6">
                <x-input-label for="kode_dosen" :value="__('Kode Dosen')" class="text-gray-700 font-semibold mb-2" />
                <x-text-input id="kode_dosen" 
                    class="block mt-1 w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg" 
                    type="text" 
                    name="kode_dosen" 
                    :value="old('kode_dosen', $dosen->kode_dosen)" 
                    required
                    placeholder="Masukkan kode dosen (contoh: DOS001)" />
                <p class="text-xs text-gray-500 mt-1">Kode dosen harus unik</p>
                <x-input-error :messages="$errors->get('kode_dosen')" class="mt-2" />
            </div>

            <!-- Prodi -->
            <div class="mb-6">
                <x-input-label for="prodi" :value="__('Program Studi')" class="text-gray-700 font-semibold mb-2" />
                <select id="prodi" 
                    name="prodi" 
                    required
                    class="block mt-1 w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm">
                    <option value="">Pilih Program Studi</option>
                    <option value="S1 Sistem Informasi" {{ old('prodi', $dosen->prodi) === 'S1 Sistem Informasi' ? 'selected' : '' }}>S1 Sistem Informasi</option>
                    <option value="S1 Teknik Industri" {{ old('prodi', $dosen->prodi) === 'S1 Teknik Industri' ? 'selected' : '' }}>S1 Teknik Industri</option>
                    <option value="S1 Digital Supply Chain" {{ old('prodi', $dosen->prodi) === 'S1 Digital Supply Chain' ? 'selected' : '' }}>S1 Digital Supply Chain</option>
                    <option value="S1 Manajemen Rekayasa" {{ old('prodi', $dosen->prodi) === 'S1 Manajemen Rekayasa' ? 'selected' : '' }}>S1 Manajemen Rekayasa</option>
                    <option value="S2 Sistem Informasi" {{ old('prodi', $dosen->prodi) === 'S2 Sistem Informasi' ? 'selected' : '' }}>S2 Sistem Informasi</option>
                    <option value="S2 Teknik Industri" {{ old('prodi', $dosen->prodi) === 'S2 Teknik Industri' ? 'selected' : '' }}>S2 Teknik Industri</option>
                </select>
                <x-input-error :messages="$errors->get('prodi')" class="mt-2" />
            </div>

            <!-- NIP -->
            <div class="mb-6">
                <x-input-label for="nip" :value="__('NIP')" class="text-gray-700 font-semibold mb-2" />
                <x-text-input id="nip" 
                    class="block mt-1 w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg" 
                    type="text" 
                    name="nip" 
                    :value="old('nip', $dosen->nip)" 
                    required
                    placeholder="Masukkan Nomor Induk Pegawai" />
                <p class="text-xs text-gray-500 mt-1">NIP harus unik</p>
                <x-input-error :messages="$errors->get('nip')" class="mt-2" />
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
                <x-button variant="ghost" href="{{ route('admin.dosens') }}">
                    Batal
                </x-button>
                <x-button variant="primary" type="submit" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'>
                    Perbarui Data Dosen
                </x-button>
            </div>
        </form>
    </div>
</div>
@endsection
