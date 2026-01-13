@extends('layouts.dashboard')

@section('title', 'Tambah User Baru')
@section('page-title', 'Tambah User Baru')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-green-600 hover:text-green-700">Dashboard</a>
    <span class="mx-2 text-gray-400">/</span>
    <a href="{{ route('admin.users') }}" class="text-green-600 hover:text-green-700">Manage Users</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Tambah User Baru</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Tambah User Baru</h2>
            <p class="text-gray-600">Buat akun baru untuk admin atau mahasiswa</p>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" id="create-user-form">
            @csrf

            <!-- Role Selection -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">Tipe User *</label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="role" value="admin" class="sr-only peer" onchange="toggleStudentFields()" required>
                        <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-green-600 peer-checked:bg-green-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center text-white font-bold">A</div>
                                <div>
                                    <div class="font-semibold text-gray-900">Administrator</div>
                                    <div class="text-xs text-gray-500">Akses penuh ke sistem</div>
                                </div>
                            </div>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="role" value="student" class="sr-only peer" onchange="toggleStudentFields()" required>
                        <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-green-600 peer-checked:bg-green-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold">S</div>
                                <div>
                                    <div class="font-semibold text-gray-900">Mahasiswa</div>
                                    <div class="text-xs text-gray-500">Akses pengajuan yudisium</div>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>

            <!-- Basic User Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <x-input-label for="name" :value="__('Nama Lengkap')" class="text-gray-700 font-semibold mb-2" />
                    <x-text-input id="name" 
                        class="block mt-1 w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg" 
                        type="text" 
                        name="name" 
                        :value="old('name')" 
                        required 
                        autofocus
                        placeholder="Nama lengkap" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-semibold mb-2" />
                    <x-text-input id="email" 
                        class="block mt-1 w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg" 
                        type="email" 
                        name="email" 
                        :value="old('email')" 
                        required
                        placeholder="email@example.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-semibold mb-2" />
                    <x-text-input id="password" 
                        class="block mt-1 w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg"
                        type="password"
                        name="password"
                        required
                        placeholder="••••••••" />
                    <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter</p>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" class="text-gray-700 font-semibold mb-2" />
                    <x-text-input id="password_confirmation" 
                        class="block mt-1 w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg"
                        type="password"
                        name="password_confirmation" 
                        required
                        placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <!-- Student Specific Fields -->
            <div id="student-fields" class="hidden border-t border-gray-200 pt-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Mahasiswa</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="nim" :value="__('NIM')" class="text-gray-700 font-semibold mb-2" />
                        <x-text-input id="nim" 
                            class="block mt-1 w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg" 
                            type="text" 
                            name="nim" 
                            :value="old('nim')"
                            placeholder="Nomor Induk Mahasiswa" />
                        <x-input-error :messages="$errors->get('nim')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="nama" :value="__('Nama Mahasiswa')" class="text-gray-700 font-semibold mb-2" />
                        <x-text-input id="nama" 
                            class="block mt-1 w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg" 
                            type="text" 
                            name="nama" 
                            :value="old('nama')"
                            placeholder="Nama lengkap sesuai data akademik" />
                        <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="ipk" :value="__('IPK')" class="text-gray-700 font-semibold mb-2" />
                        <x-text-input id="ipk" 
                            class="block mt-1 w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg" 
                            type="number" 
                            name="ipk" 
                            step="0.01"
                            min="0"
                            max="4"
                            :value="old('ipk')"
                            placeholder="Indeks Prestasi Kumulatif (0.00–4.00)" />
                        <x-input-error :messages="$errors->get('ipk')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="total_sks" :value="__('Total SKS')" class="text-gray-700 font-semibold mb-2" />
                        <x-text-input id="total_sks" 
                            class="block mt-1 w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg" 
                            type="number" 
                            name="total_sks" 
                            min="0"
                            :value="old('total_sks')"
                            placeholder="Total Satuan Kredit Semester" />
                        <x-input-error :messages="$errors->get('total_sks')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.users') }}" class="text-gray-600 hover:text-gray-900 font-medium">
                    Batal
                </a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-6 rounded-lg transition shadow-lg hover:shadow-xl">
                    Simpan User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleStudentFields() {
        const studentRole = document.querySelector('input[name="role"][value="student"]');
        const studentFields = document.getElementById('student-fields');
        const nimField = document.getElementById('nim');
        const namaField = document.getElementById('nama');
        
        if (studentRole.checked) {
            studentFields.classList.remove('hidden');
            nimField.setAttribute('required', 'required');
            namaField.setAttribute('required', 'required');
        } else {
            studentFields.classList.add('hidden');
            nimField.removeAttribute('required');
            namaField.removeAttribute('required');
            // Clear student fields
            document.getElementById('nim').value = '';
            document.getElementById('nama').value = '';
            document.getElementById('ipk').value = '';
            document.getElementById('total_sks').value = '';
        }
    }

    // Check if student role was previously selected (for validation errors)
    document.addEventListener('DOMContentLoaded', function() {
        const studentRole = document.querySelector('input[name="role"][value="student"]');
        if (studentRole && studentRole.checked) {
            toggleStudentFields();
        }
        
        // Auto-select student role if there are student field errors
        @if($errors->has('nim') || $errors->has('nama'))
            if (!studentRole.checked) {
                studentRole.click();
            }
        @endif
    });
</script>
@endsection

