@extends('layouts.dashboard')

@section('title', 'Import Users')
@section('page-title', 'Import Users')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-green-600 hover:text-green-700">Dashboard</a>
    <span class="mx-2 text-gray-400">/</span>
    <a href="{{ route('admin.users') }}" class="text-green-600 hover:text-green-700">Manage Users</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Import Users</span>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Import Users dari File</h2>
            <p class="text-gray-600">Import user dalam jumlah banyak dari file CSV atau Excel</p>
        </div>

        @if(session('error'))
            <x-alert type="error" dismissible>{{ session('error') }}</x-alert>
        @endif

        <!-- Instructions -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-r mb-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Format File CSV
            </h3>
            <p class="text-sm text-blue-800 mb-4">File CSV harus memiliki header dengan kolom berikut:</p>
            <div class="bg-white p-4 rounded-lg border border-blue-200 mb-4">
                <code class="text-sm text-gray-800">
                name,email,password,role,nim,prodi,tak,skor_eprt,dosen_wali,pembimbing_1,pembimbing_2,penguji_ketua,penguji_anggota,ipk,total_sks
                </code>
            </div>
            <div class="space-y-2 text-sm text-blue-800">
                <p><strong>Kolom Wajib:</strong></p>
                <ul class="list-disc list-inside ml-4 space-y-1">
                    <li><code class="bg-blue-100 px-1 rounded">name</code> - Nama lengkap user</li>
                    <li><code class="bg-blue-100 px-1 rounded">email</code> - Email user (harus unique)</li>
                    <li><code class="bg-blue-100 px-1 rounded">role</code> - Role user: <code class="bg-blue-100 px-1 rounded">admin</code> atau <code class="bg-blue-100 px-1 rounded">student</code></li>
                </ul>
                <p class="mt-3"><strong>Kolom Opsional:</strong></p>
                <ul class="list-disc list-inside ml-4 space-y-1">
                    <li><code class="bg-blue-100 px-1 rounded">password</code> - Password (default: password123 jika kosong)</li>
                    <li><code class="bg-blue-100 px-1 rounded">nim</code> - NIM mahasiswa (wajib jika role = student)</li>
                    <li><code class="bg-blue-100 px-1 rounded">ipk</code> - IPK (default: 0)</li>
                    <li><code class="bg-blue-100 px-1 rounded">total_sks</code> - Total SKS (default: 0)</li>
                    <li><code class="bg-blue-100 px-1 rounded">prodi</code> - Program studi mahasiswa</li>
                    <li><code class="bg-blue-100 px-1 rounded">tak</code> - Total AK / TAK mahasiswa</li>
                    <li><code class="bg-blue-100 px-1 rounded">skor_eprt</code> - Skor EPRT (310-677)</li>
                    <li><code class="bg-blue-100 px-1 rounded">dosen_wali</code> - Nama dosen wali</li>
                    <li><code class="bg-blue-100 px-1 rounded">pembimbing_1</code> - Nama pembimbing 1</li>
                    <li><code class="bg-blue-100 px-1 rounded">pembimbing_2</code> - Nama pembimbing 2</li>
                    <li><code class="bg-blue-100 px-1 rounded">penguji_ketua</code> - Nama penguji ketua</li>
                    <li><code class="bg-blue-100 px-1 rounded">penguji_anggota</code> - Nama penguji anggota</li>
                </ul>
            </div>
        </div>

        <!-- Example CSV -->
        <div class="bg-gray-50 border border-gray-200 p-6 rounded-lg mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Contoh Format CSV
            </h3>
            <div class="bg-white p-4 rounded border border-gray-300 overflow-x-auto">
                <pre class="text-xs text-gray-700">name,email,password,role,nim,prodi,tak,skor_eprt,dosen_wali,pembimbing_1,pembimbing_2,penguji_ketua,penguji_anggota,ipk,total_sks
John Doe,john@example.com,password123,student,123456789,Sistem Informasi,120,3.50,Dr. Andi,Dr. Budi,Dr. Citra,Dr. Dedi,Dr. Eka,3.75,144
Jane Smith,jane@example.com,password123,student,987654321,Informatika,115,3.60,Dr. Rina,Dr. Agus,,Dr. Rudi,Dr. Sari,3.80,144
Admin User,admin@example.com,password123,admin,,,,,,,,,,,,
</pre>
            </div>
            <p class="text-xs text-gray-600 mt-3 flex items-start gap-1">
                <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span><strong>Tips:</strong> Pastikan tidak ada spasi di header, gunakan koma sebagai pemisah, dan simpan file dengan encoding UTF-8</span>
            </p>
        </div>

        <!-- Upload Form -->
        <form action="{{ route('admin.users.import.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-6">
                <x-input-label for="file" :value="__('Pilih File CSV/Excel')" class="text-gray-700 font-semibold mb-2" />
                <input type="file" 
                    id="file" 
                    name="file" 
                    accept=".csv,.xlsx,.xls"
                    required
                    class="block mt-1 w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer focus:border-green-500 focus:ring-green-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
                    onchange="previewFileName(this)">
                <p class="text-xs text-gray-500 mt-2">Format yang didukung: CSV, XLSX, XLS. Maksimal 5MB</p>
                <div id="file-name" class="mt-2 text-sm text-gray-600 hidden"></div>
                <x-input-error :messages="$errors->get('file')" class="mt-2" />
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
                <x-button variant="ghost" href="{{ route('admin.users') }}">
                    Batal
                </x-button>
                <x-button variant="primary" type="submit" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>'>
                    Import Users
                </x-button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewFileName(input) {
        const fileNameDiv = document.getElementById('file-name');
        if (input.files && input.files[0]) {
            fileNameDiv.textContent = 'File dipilih: ' + input.files[0].name;
            fileNameDiv.classList.remove('hidden');
        } else {
            fileNameDiv.classList.add('hidden');
        }
    }
</script>
@endsection

