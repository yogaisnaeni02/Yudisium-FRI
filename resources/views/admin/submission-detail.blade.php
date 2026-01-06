@extends('layouts.app')

@section('content')
<div class="container max-w-6xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Detail Pengajuan Yudisium</h1>
            <p class="text-gray-600 mt-2">{{ $submission->student->user->name }} ({{ $submission->student->nim }})</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-700">← Kembali</a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Status & Progress -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-semibold text-gray-600 mb-2">STATUS PENGAJUAN</h3>
            <div class="inline-block px-4 py-2 rounded-full text-sm font-semibold"
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
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-semibold text-gray-600 mb-2">PROGRESS DOKUMEN</h3>
            <div class="flex items-center gap-4">
                <div class="text-3xl font-bold text-blue-600">{{ $submission->getProgressPercentage() }}%</div>
                <div class="flex-1">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $submission->getProgressPercentage() }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-semibold text-gray-600 mb-2">TOTAL DOKUMEN</h3>
            <p class="text-3xl font-bold text-gray-900">{{ $submission->documents->count() }}</p>
            <p class="text-xs text-gray-600 mt-1">
                {{ $submission->documents->where('status', 'approved')->count() }} disetujui
            </p>
        </div>
    </div>

    <!-- Info Mahasiswa -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">INFORMASI MAHASISWA</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-sm text-gray-600">NIM</p>
                <p class="font-semibold text-gray-900">{{ $submission->student->nim }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Nama</p>
                <p class="font-semibold text-gray-900">{{ $submission->student->user->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">IPK</p>
                <p class="font-semibold text-gray-900">{{ $submission->student->ipk }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Total SKS</p>
                <p class="font-semibold text-gray-900">{{ $submission->student->total_sks }}</p>
            </div>
        </div>
    </div>

    <!-- Dokumen -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-blue-600">
            <h3 class="text-lg font-semibold text-white">Daftar Dokumen</h3>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($submission->documents as $document)
                <div class="p-6 hover:bg-gray-50">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900">{{ $document->type }}</h4>
                            <p class="text-sm text-gray-600">{{ $document->name }}</p>
                        </div>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold"
                            @switch($document->status)
                                @case('pending')
                                    style="background-color: #f3f4f6; color: #4b5563;"
                                    @break
                                @case('approved')
                                    style="background-color: #dcfce7; color: #166534;"
                                    @break
                                @case('revision')
                                    style="background-color: #fef3c7; color: #92400e;"
                                    @break
                                @case('rejected')
                                    style="background-color: #fee2e2; color: #991b1b;"
                                    @break
                            @endswitch
                        >
                            {{ ucfirst($document->status) }}
                        </span>
                    </div>

                    <!-- Document Info -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 text-sm text-gray-600">
                        <div>
                            <p class="text-xs text-gray-500">Diupload</p>
                            <p class="font-medium">{{ $document->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Terakhir Diubah</p>
                            <p class="font-medium">{{ $document->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.download-document', $document) }}" class="text-blue-600 hover:underline font-medium">
                                📥 Download File
                            </a>
                        </div>
                    </div>

                    <!-- Feedback Section -->
                    @if($document->feedback)
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                        <p class="text-sm text-gray-700"><strong>Feedback Admin:</strong></p>
                        <p class="text-sm text-gray-700">{{ $document->feedback }}</p>
                    </div>
                    @endif

                    <!-- Update Status Form -->
                    <form action="{{ route('admin.update-document-status', $document) }}" method="POST" class="mt-4">
                        @csrf
                        @method('PATCH')
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ubah Status</label>
                                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Pilih Status</option>
                                    <option value="approved" {{ $document->status === 'approved' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="revision" {{ $document->status === 'revision' ? 'selected' : '' }}>Perlu Revisi</option>
                                    <option value="rejected" {{ $document->status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan/Feedback</label>
                                <input type="text" name="feedback" placeholder="Catatan untuk mahasiswa..." 
                                    value="{{ $document->feedback }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                                    Simpan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @empty
                <div class="p-6 text-center text-gray-600">
                    Tidak ada dokumen
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
