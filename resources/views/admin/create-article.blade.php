@extends('layouts.dashboard')

@section('title', 'Tambah Informasi Yudisium')
@section('page-title', 'Tambah Informasi Yudisium')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-green-600 hover:text-green-700">Dashboard</a>
    <span class="mx-2 text-gray-400">/</span>
    <a href="{{ route('admin.articles') }}" class="text-green-600 hover:text-green-700">Informasi Yudisium</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Tambah Informasi Baru</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Tambah Informasi Yudisium</h2>
            <p class="text-gray-600">Buat informasi yudisium untuk mahasiswa</p>
        </div>

        <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Title -->
            <div class="mb-6">
                <x-input-label for="title" :value="__('Judul Informasi')" class="text-gray-700 font-semibold mb-2" />
                <x-text-input id="title" 
                    class="block mt-1 w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg" 
                    type="text" 
                    name="title" 
                    :value="old('title')" 
                    required 
                    autofocus
                    placeholder="Masukkan judul informasi" />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <!-- Excerpt -->
            <div class="mb-6">
                <x-input-label for="excerpt" :value="__('Ringkasan Informasi')" class="text-gray-700 font-semibold mb-2" />
                <textarea id="excerpt" 
                    name="excerpt" 
                    rows="3"
                    class="block mt-1 w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg"
                    placeholder="Ringkasan singkat informasi (opsional)">{{ old('excerpt') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Maksimal 500 karakter</p>
                <x-input-error :messages="$errors->get('excerpt')" class="mt-2" />
            </div>

            <!-- Content -->
            <div class="mb-6">
                <x-input-label for="content" :value="__('Isi Informasi')" class="text-gray-700 font-semibold mb-2" />
                <textarea id="content" 
                    name="content" 
                    rows="15"
                    class="block mt-1 w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg"
                    required
                    placeholder="Tulis isi informasi di sini...">{{ old('content') }}</textarea>
                <x-input-error :messages="$errors->get('content')" class="mt-2" />
            </div>

            <!-- Image -->
            <div class="mb-6">
                <x-input-label for="image" :value="__('Gambar Cover')" class="text-gray-700 font-semibold mb-2" />
                <input type="file" 
                    id="image" 
                    name="image" 
                    accept="image/*"
                    class="block mt-1 w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer focus:border-green-500 focus:ring-green-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
                    onchange="previewImage(this)">
                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 2MB</p>
                <div id="image-preview" class="mt-3 hidden">
                    <img id="preview-img" src="" alt="Preview" class="max-w-xs rounded-lg border border-gray-300">
                </div>
                <x-input-error :messages="$errors->get('image')" class="mt-2" />
            </div>

            <!-- Status -->
            <div class="mb-6">
                <x-input-label for="status" :value="__('Status')" class="text-gray-700 font-semibold mb-2" />
                <select id="status" 
                    name="status" 
                    class="block mt-1 w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg"
                    required>
                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                </select>
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.articles') }}" class="text-gray-600 hover:text-gray-900 font-medium">
                    Batal
                </a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-6 rounded-lg transition shadow-lg hover:shadow-xl">
                    Simpan Informasi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.classList.remove('hidden');
            }
            
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.classList.add('hidden');
        }
    }
</script>
@endsection

