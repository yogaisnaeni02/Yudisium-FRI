@extends('layouts.app')

@section('content')
<div class="container max-w-4xl mx-auto px-4 py-16 text-center">
    <div class="bg-white rounded-lg shadow p-12">
        <div class="mb-6">
            <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Data Mahasiswa Tidak Ditemukan</h2>
        <p class="text-gray-600 mb-6">Silakan hubungi administrator untuk melakukan pendaftaran sebagai mahasiswa.</p>
        <a href="{{ route('dashboard') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
            Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
