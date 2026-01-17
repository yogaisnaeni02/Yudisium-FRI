@extends('layouts.dashboard')

@section('title', 'Detail Pengajuan')
@section('page-title', 'Detail Pengajuan Yudisium')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-green-600 hover:text-green-700">Dashboard</a>
    <span class="mx-2 text-gray-400">/</span>
    <a href="{{ route('admin.verifikasi-pengajuan') }}" class="text-green-600 hover:text-green-700">Verifikasi Pengajuan</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Detail Pengajuan</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Status & Progress Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
            <h3 class="text-sm font-semibold text-gray-600 mb-3">STATUS PENGAJUAN</h3>
            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold"
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
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
            <h3 class="text-sm font-semibold text-gray-600 mb-3">PROGRESS DOKUMEN</h3>
            <div class="flex items-center gap-4">
                <div class="text-3xl font-bold text-green-600">{{ $submission->getProgressPercentage() }}%</div>
                <div class="flex-1">
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-gradient-to-r from-green-500 to-green-600 h-3 rounded-full transition-all" style="width: {{ $submission->getProgressPercentage() }}%"></div>
                    </div>
                </div>
            </div>
            <p class="text-xs text-gray-600 mt-3">
                {{ $submission->documents->where('status', 'approved')->count() }} dari {{ $submission->documents->count() }} dokumen disetujui
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
            <h3 class="text-sm font-semibold text-gray-600 mb-3">TOTAL DOKUMEN</h3>
            <p class="text-3xl font-bold text-gray-900">{{ $submission->documents->count() }}</p>
            <p class="text-xs text-gray-600 mt-2">
                @if($submission->submitted_at)
                    Dikirim: {{ $submission->submitted_at->format('d M Y H:i') }}
                @else
                    Belum dikirim
                @endif
            </p>
        </div>
    </div>

    <!-- Info Mahasiswa -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            INFORMASI MAHASISWA
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div>
                <p class="text-sm text-gray-600 mb-1">NIM</p>
                <p class="font-semibold text-gray-900 text-lg">{{ $submission->student->nim }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Nama</p>
                <p class="font-semibold text-gray-900 text-lg">{{ $submission->student->nama ?? $submission->student->user->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">IPK</p>
                <p class="font-semibold text-gray-900 text-lg">{{ $submission->student->ipk }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Total SKS</p>
                <p class="font-semibold text-gray-900 text-lg">{{ $submission->student->total_sks }}</p>
            </div>
        </div>
    </div>

    <!-- Periode & Verifikasi Section - Only show when all documents are approved -->
    @php
        $allDocumentsApproved = $submission->documents()->count() > 0 && 
                                $submission->documents()->where('status', '!=', 'approved')->doesntExist();
        $progressPercentage = $submission->getProgressPercentage();
    @endphp

    @if($allDocumentsApproved || $progressPercentage === 100)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">PERIODE & VERIFIKASI</h3>
        <form action="{{ route('admin.update-verification', $submission) }}" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Periode Selection -->
                <div>
                    <label for="periode_id" class="block text-sm font-semibold text-gray-700 mb-2">Periode Yudisium</label>
                    <select id="periode_id" name="periode_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white">
                        <option value="">Pilih Periode...</option>
                        @foreach(\App\Models\Periode::orderBy('tanggal_mulai', 'desc')->get() as $periode)
                            <option value="{{ $periode->id }}" {{ $submission->periode_id === $periode->id ? 'selected' : '' }}>
                                {{ $periode->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Predikat Selection -->
                <div>
                    <label for="predikat" class="block text-sm font-semibold text-gray-700 mb-2">Predikat Kelulusan</label>
                    <select id="predikat" name="predikat" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white">
                        <option value="">Tanpa Predikat</option>
                        <option value="memuaskan" {{ isset($yudisiumResult) && $yudisiumResult->predikat_kelulusan === 'memuaskan' ? 'selected' : '' }}>Memuaskan</option>
                        <option value="sangat_memuaskan" {{ isset($yudisiumResult) && $yudisiumResult->predikat_kelulusan === 'sangat_memuaskan' ? 'selected' : '' }}>Sangat Memuaskan</option>
                        <option value="cumlaude" {{ isset($yudisiumResult) && $yudisiumResult->predikat_kelulusan === 'cumlaude' ? 'selected' : '' }}>Dengan Pujian (Cumlaude)</option>
                        <option value="summa_cumlaude" {{ isset($yudisiumResult) && $yudisiumResult->predikat_kelulusan === 'summa_cumlaude' ? 'selected' : '' }}>Sempurna (Summa Cumlaude)</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <x-button variant="primary" type="submit" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'>
                    Simpan Periode & Predikat
                </x-button>
            </div>
        </form>
    </div>
    @else
    <!-- Info message when documents are not all approved -->
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-lg">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-yellow-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div>
                <h3 class="text-lg font-semibold text-yellow-800 mb-2">Periode & Verifikasi Belum Tersedia</h3>
                <p class="text-yellow-700">
                    Bagian Periode & Verifikasi akan muncul setelah semua dokumen mahasiswa telah disetujui (Progress: {{ $progressPercentage }}%).
                    <br>
                    <span class="text-sm mt-1 block">
                        Saat ini: {{ $submission->documents->where('status', 'approved')->count() }} dari {{ $submission->documents->count() }} dokumen telah disetujui.
                    </span>
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Dokumen List - Grouped -->
    @php
        $groups = [
            'Berkas Identitas & Akademik' => [
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>',
                'docs' => [
                    'Surat Pernyataan',
                    'Form Biodata Izajah & Transkip',
                    'KTP',
                    'Akta Lahir',
                    'Ijazah Pendidikan Terakhir',
                ],
            ],
            'Berkas Akademik & Karya' => [
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>',
                'docs' => [
                    'Buku TA yang Disahkan',
                    'Slide PPT',
                    'Screenshot (Gracias)',
                ],
            ],
            'Berkas Pendukung' => [
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>',
                'docs' => [
                    'Berkas Referensi (Minimal 10)',
                    'Bukti Approval Revisi (SOFI)',
                    'Bukti Approval SKPI',
                    'Surat Keterangan Bebas Pustaka (SKBP)',
                    'Dokumen Cumlaude (Publikasi/Lomba/HKI)',
                    'Dokumen Pendukung Tambahan',
                ],
            ],
        ];
    @endphp

    <!-- Group Navigation Buttons -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex flex-wrap gap-2">
            @foreach($groups as $groupName => $groupData)
                @php
                    $groupSlug = \Illuminate\Support\Str::slug($groupName);
                    $groupDocuments = $submission->documents->filter(function($doc) use ($groupData) {
                        return in_array($doc->type, $groupData['docs']);
                    });
                @endphp
                @if($groupDocuments->count() > 0)
                <button type="button" 
                    onclick="showGroup('{{ $groupSlug }}')" 
                    data-group="{{ $groupSlug }}"
                    class="group-btn flex items-center gap-2 px-4 py-2.5 rounded-lg font-medium text-sm transition-all shadow-sm hover:shadow-md {{ $loop->first ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {!! $groupData['icon'] !!}
                    <span>{{ $groupName }}</span>
                    <span class="px-2 py-0.5 rounded-full text-xs {{ $loop->first ? 'bg-white/20 text-white' : 'bg-gray-300 text-gray-700' }}">
                        {{ $groupDocuments->count() }}
                    </span>
                </button>
                @endif
            @endforeach
        </div>
    </div>

    <form id="batchUpdateForm" action="{{ route('admin.batch-update-documents', $submission) }}" method="POST" class="w-full">
        @csrf
        @method('PATCH')
        <div class="space-y-6 w-full">
            @foreach($groups as $groupName => $groupData)
                @php
                    $groupSlug = \Illuminate\Support\Str::slug($groupName);
                    $groupDocuments = $submission->documents->filter(function($doc) use ($groupData) {
                        return in_array($doc->type, $groupData['docs']);
                    });
                @endphp
                
                @if($groupDocuments->count() > 0)
                <div class="group-content bg-white rounded-xl shadow-sm border border-gray-200 {{ $loop->first ? '' : 'hidden' }}" data-group="{{ $groupSlug }}">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            {!! $groupData['icon'] !!}
                            <span>{{ $groupName }}</span>
                            <span class="text-sm font-normal text-blue-100 ml-2">
                                ({{ $groupDocuments->count() }} dokumen)
                            </span>
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($groupDocuments as $document)
                            <div class="p-6 hover:bg-blue-50/50 transition-all duration-200 border-b border-gray-200 last:border-b-0 first:border-t-0">
                                <!-- Header: Title, Filename, and Status Badge -->
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-base font-bold text-gray-900 mb-1.5">{{ $document->type }}</h4>
                                        <p class="text-sm text-gray-600 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span class="truncate">{{ $document->name }}</span>
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold flex-shrink-0 whitespace-nowrap shadow-sm status-badge-{{ $document->id }}"
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
                                        <span class="status-text-{{ $document->id }}">{{ ucfirst($document->status) }}</span>
                                    </span>
                                </div>

                                <!-- Metadata Row -->
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5 text-sm">
                                    <div class="flex items-center gap-4">
                                        <div class="flex items-center gap-1.5 text-gray-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span class="text-sm">{{ $document->created_at->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('admin.preview-document', $document) }}" target="_blank" class="text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1.5 whitespace-nowrap text-sm hover:underline">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Preview
                                        </a>
                                        <span class="text-gray-300">|</span>
                                        <a href="{{ route('admin.download-document', $document) }}" class="text-gray-600 hover:text-gray-700 font-medium flex items-center gap-1.5 whitespace-nowrap text-sm hover:underline">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            Download
                                        </a>
                                    </div>
                                </div>

                                <!-- Feedback Section -->
                                @if($document->feedback)
                                <div class="bg-yellow-50 border-l-4 border-yellow-400 px-4 py-3.5 mb-5 rounded-r-lg shadow-sm">
                                    <p class="text-sm font-semibold text-yellow-900 mb-2 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                        Feedback:
                                    </p>
                                    <p class="text-sm text-yellow-800 leading-relaxed">{{ $document->feedback }}</p>
                                </div>
                                @endif

                                <!-- Update Status Form -->
                                <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-5 rounded-lg border border-gray-200 mt-5">
                                    <div class="grid grid-cols-1 sm:grid-cols-5 gap-4 items-end">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2.5">Status</label>
                                            <select name="documents[{{ $document->id }}][status]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm doc-status bg-white shadow-sm hover:border-gray-400 transition" data-doc-id="{{ $document->id }}" data-group="{{ $groupSlug }}">
                                                <option value="">-- Pilih Status --</option>
                                                <option value="approved" {{ $document->status === 'approved' ? 'selected' : '' }}>✓ Setujui</option>
                                                <option value="revision" {{ $document->status === 'revision' ? 'selected' : '' }}>⟳ Revisi</option>
                                                <option value="rejected" {{ $document->status === 'rejected' ? 'selected' : '' }}>✕ Tolak</option>
                                            </select>
                                        </div>
                                        <div class="sm:col-span-4">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2.5">Catatan (Opsional)</label>
                                            <input type="text" name="documents[{{ $document->id }}][feedback]" placeholder="Masukkan catatan atau feedback..." 
                                                value="{{ $document->feedback }}"
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm bg-white shadow-sm hover:border-gray-400 transition" data-group="{{ $groupSlug }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Save Button for Section -->
                    <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-green-100 border-t-2 border-green-200 flex justify-end">
                        <x-button variant="primary" type="button" onclick="submitGroupForm('{{ $groupSlug }}')" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'>
                            Simpan {{ $groupName }}
                        </x-button>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </form>

        <!-- Check for documents not in any group -->
        @php
            $allGroupedTypes = [];
            foreach($groups as $groupData) {
                $allGroupedTypes = array_merge($allGroupedTypes, $groupData['docs']);
            }
            $ungroupedDocuments = $submission->documents->filter(function($doc) use ($allGroupedTypes) {
                return !in_array($doc->type, $allGroupedTypes);
            });
        @endphp

        @if($ungroupedDocuments->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-600 to-gray-700">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Dokumen Lainnya ({{ $ungroupedDocuments->count() }})
                </h3>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($ungroupedDocuments as $document)
                    <div class="p-6 hover:bg-gray-50/50 transition-all duration-200 border-b border-gray-200 last:border-b-0 first:border-t-0">
                        <!-- Header: Title, Filename, and Status Badge -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-base font-bold text-gray-900 mb-1.5">{{ $document->type }}</h4>
                                <p class="text-sm text-gray-600 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="truncate">{{ $document->name }}</span>
                                </p>
                            </div>
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold flex-shrink-0 whitespace-nowrap shadow-sm status-badge-{{ $document->id }}"
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
                                <span class="status-text-{{ $document->id }}">{{ ucfirst($document->status) }}</span>
                            </span>
                        </div>

                        <!-- Metadata Row -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5 text-sm">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-1.5 text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="text-sm">{{ $document->created_at->format('d M Y') }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.preview-document', $document) }}" target="_blank" class="text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1.5 whitespace-nowrap text-sm hover:underline">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Preview
                                </a>
                                <span class="text-gray-300">|</span>
                                <a href="{{ route('admin.download-document', $document) }}" class="text-gray-600 hover:text-gray-700 font-medium flex items-center gap-1.5 whitespace-nowrap text-sm hover:underline">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Download
                                </a>
                            </div>
                        </div>

                        <!-- Feedback Section -->
                        @if($document->feedback)
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 px-4 py-3.5 mb-5 rounded-r-lg shadow-sm">
                            <p class="text-sm font-semibold text-yellow-900 mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                Feedback:
                            </p>
                            <p class="text-sm text-yellow-800 leading-relaxed">{{ $document->feedback }}</p>
                        </div>
                        @endif

                        <!-- Update Status Form -->
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-5 rounded-lg border border-gray-200 mt-5">
                            <div class="grid grid-cols-1 sm:grid-cols-5 gap-4 items-end">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2.5">Status</label>
                                    <select name="documents[{{ $document->id }}][status]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm doc-status bg-white shadow-sm hover:border-gray-400 transition" data-doc-id="{{ $document->id }}" data-group="dokumen-lainnya">
                                        <option value="">-- Pilih Status --</option>
                                        <option value="approved" {{ $document->status === 'approved' ? 'selected' : '' }}>✓ Setujui</option>
                                        <option value="revision" {{ $document->status === 'revision' ? 'selected' : '' }}>⟳ Revisi</option>
                                        <option value="rejected" {{ $document->status === 'rejected' ? 'selected' : '' }}>✕ Tolak</option>
                                    </select>
                                </div>
                                <div class="sm:col-span-4">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2.5">Catatan (Opsional)</label>
                                    <input type="text" name="documents[{{ $document->id }}][feedback]" placeholder="Masukkan catatan atau feedback..." 
                                        value="{{ $document->feedback }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm bg-white shadow-sm hover:border-gray-400 transition" data-group="dokumen-lainnya">
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Save Button for Section -->
            <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-green-100 border-t-2 border-green-200 flex justify-end">
                <x-button variant="primary" type="button" onclick="submitGroupForm('dokumen-lainnya')" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'>
                    Simpan Dokumen Lainnya
                </x-button>
            </div>
        </div>
        @endif

        @if($submission->documents->count() === 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center text-gray-600">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-lg font-medium">Tidak ada dokumen untuk pengajuan ini</p>
            <p class="text-sm mt-2">Mahasiswa belum mengunggah dokumen apapun</p>
        </div>
        @endif
    </div>
</div>

<script>
    function showGroup(groupSlug) {
        // Hide all group contents
        document.querySelectorAll('.group-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Remove active style from all buttons
        document.querySelectorAll('.group-btn').forEach(btn => {
            btn.classList.remove('bg-green-600', 'text-white');
            btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
            
            // Update badge style
            const badge = btn.querySelector('span:last-child');
            if (badge) {
                badge.classList.remove('bg-white/20', 'text-white');
                badge.classList.add('bg-gray-300', 'text-gray-700');
            }
        });
        
        // Show selected group content
        const selectedContent = document.querySelector(`.group-content[data-group="${groupSlug}"]`);
        if (selectedContent) {
            selectedContent.classList.remove('hidden');
        }
        
        // Add active style to selected button
        const selectedBtn = document.querySelector(`.group-btn[data-group="${groupSlug}"]`);
        if (selectedBtn) {
            selectedBtn.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
            selectedBtn.classList.add('bg-green-600', 'text-white');
            
            // Update badge style
            const badge = selectedBtn.querySelector('span:last-child');
            if (badge) {
                badge.classList.remove('bg-gray-300', 'text-gray-700');
                badge.classList.add('bg-white/20', 'text-white');
            }
        }
        
        // Smooth scroll to top of content
        if (selectedContent) {
            setTimeout(() => {
                selectedContent.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }
    }

    function submitGroupForm(groupSlug) {
        // Get all inputs related to this group
        const groupInputs = document.querySelectorAll(`[data-group="${groupSlug}"]`);
        
        console.log('Group slug:', groupSlug);
        console.log('Found inputs:', groupInputs.length);
        
        if (groupInputs.length === 0) {
            alert('Tidak ada perubahan untuk disimpan.');
            return;
        }

        // Check if any status changes were made
        let hasChanges = false;
        const documentsMap = {};
        
        groupInputs.forEach((input, idx) => {
            const inputName = input.name || '';
            const nameMatch = inputName.match(/documents\[(\d+)\]\[(\w+)\]/);
            
            if (nameMatch) {
                const docId = nameMatch[1];
                const fieldName = nameMatch[2];
                
                if (!documentsMap[docId]) {
                    documentsMap[docId] = {};
                }
                documentsMap[docId][fieldName] = input.value;
                
                console.log(`Input ${idx}:`, inputName, '=', input.value, '(docId:', docId, ', field:', fieldName, ')');
                
                if (fieldName === 'status' && input.value) {
                    hasChanges = true;
                }
            } else {
                console.warn(`Input ${idx} does not match pattern:`, inputName);
            }
        });

        console.log('Documents map:', documentsMap);
        console.log('Has changes:', hasChanges);

        if (!hasChanges) {
            alert('Silakan pilih status untuk setidaknya satu dokumen.');
            return;
        }

        // Show confirmation dialog
        if (confirm('Apakah Anda yakin ingin menyimpan perubahan untuk section ini?')) {
            // Submit the main form with AJAX or just submit normally
            const form = document.getElementById('batchUpdateForm');
            
            // Create a temporary form to submit only this group's data
            const tempForm = document.createElement('form');
            tempForm.method = 'POST';
            tempForm.action = form.action;
            
            // Add CSRF token
            const csrfToken = document.querySelector('input[name="_token"]');
            if (csrfToken) {
                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = csrfToken.value;
                tempForm.appendChild(tokenInput);
            }
            
            // Add method
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PATCH';
            tempForm.appendChild(methodInput);
            
            // Add only this group's inputs properly
            Object.keys(documentsMap).forEach(docId => {
                Object.keys(documentsMap[docId]).forEach(fieldName => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `documents[${docId}][${fieldName}]`;
                    input.value = documentsMap[docId][fieldName];
                    tempForm.appendChild(input);
                    console.log('Adding to form:', input.name, '=', input.value);
                });
            });
            
            console.log('Form action:', tempForm.action);
            console.log('Form will submit now...');
            
            document.body.appendChild(tempForm);
            tempForm.submit();
            document.body.removeChild(tempForm);
        }
    }

    // Initialize: show first group by default
    document.addEventListener('DOMContentLoaded', function() {
        const firstBtn = document.querySelector('.group-btn');
        if (firstBtn) {
            const firstGroup = firstBtn.getAttribute('data-group');
            if (firstGroup) {
                showGroup(firstGroup);
            }
        }
    });
</script>
@endsection
