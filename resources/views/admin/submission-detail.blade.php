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
<div class="space-y-6 w-full">
    <!-- Status & Progress Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full">
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
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition w-full">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">INFORMASI MAHASISWA</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 w-full">
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

    <form id="batchUpdateForm" action="{{ route('admin.batch-update-documents', $submission) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="space-y-6">
            @foreach($groups as $groupName => $groupData)
                @php
                    $groupSlug = \Illuminate\Support\Str::slug($groupName);
                    $groupDocuments = $submission->documents->filter(function($doc) use ($groupData) {
                        return in_array($doc->type, $groupData['docs']);
                    });
                @endphp
                
                @if($groupDocuments->count() > 0)
                <div class="group-content bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden {{ $loop->first ? '' : 'hidden' }}" data-group="{{ $groupSlug }}">
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
                            <div class="p-5 hover:bg-blue-50 transition border-b border-gray-100 last:border-b-0">
                                <!-- Header: Title, Filename, and Status Badge -->
                                <div class="flex items-center justify-between gap-3 mb-3">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-bold text-gray-900 mb-0.5 truncate">{{ $document->type }}</h4>
                                        <p class="text-xs text-gray-600 flex items-center gap-1 truncate">
                                            <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span class="truncate">{{ $document->name }}</span>
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold flex-shrink-0 whitespace-nowrap status-badge-{{ $document->id }}"
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
                                <div class="flex items-center justify-between gap-3 mb-3 text-xs">
                                    <div class="flex items-center gap-4">
                                        <div class="flex items-center gap-1 text-gray-600">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>{{ $document->created_at->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.download-document', $document) }}" class="text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1 whitespace-nowrap">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        Download
                                    </a>
                                </div>

                                <!-- Feedback Section -->
                                @if($document->feedback)
                                <div class="bg-yellow-50 border-l-4 border-yellow-400 px-3 py-2 mb-3 rounded-r text-sm">
                                    <p class="text-xs font-semibold text-yellow-900 mb-1 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                        Feedback:
                                    </p>
                                    <p class="text-xs text-yellow-800">{{ $document->feedback }}</p>
                                </div>
                                @endif

                                <!-- Update Status Form -->
                                <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-3.5 rounded-lg border border-gray-200">
                                    <div class="grid grid-cols-1 sm:grid-cols-5 gap-2.5 items-end">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Status</label>
                                            <select name="documents[{{ $document->id }}][status]" class="w-full px-2.5 py-1.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent text-xs doc-status" data-doc-id="{{ $document->id }}" data-group="{{ $groupSlug }}">
                                                <option value="">Pilih</option>
                                                <option value="approved" {{ $document->status === 'approved' ? 'selected' : '' }}>✓ Setujui</option>
                                                <option value="revision" {{ $document->status === 'revision' ? 'selected' : '' }}>⟳ Revisi</option>
                                                <option value="rejected" {{ $document->status === 'rejected' ? 'selected' : '' }}>✕ Tolak</option>
                                            </select>
                                        </div>
                                        <div class="sm:col-span-4">
                                            <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Catatan (Opsional)</label>
                                            <input type="text" name="documents[{{ $document->id }}][feedback]" placeholder="Masukkan catatan..." 
                                                value="{{ $document->feedback }}"
                                                class="w-full px-2.5 py-1.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent text-xs" data-group="{{ $groupSlug }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Save Button for Section -->
                    <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-green-100 border-t-2 border-green-200 flex justify-end">
                        <button type="button" onclick="submitGroupForm('{{ $groupSlug }}')" class="bg-green-600 hover:bg-green-700 active:bg-green-800 text-white font-semibold py-2.5 px-6 rounded-lg transition shadow-md hover:shadow-lg inline-flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Simpan {{ $groupName }}</span>
                        </button>
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
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
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
                    <div class="p-5 hover:bg-gray-50 transition border-b border-gray-100 last:border-b-0">
                        <!-- Header: Title, Filename, and Status Badge -->
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-bold text-gray-900 mb-0.5 truncate">{{ $document->type }}</h4>
                                <p class="text-xs text-gray-600 flex items-center gap-1 truncate">
                                    <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="truncate">{{ $document->name }}</span>
                                </p>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold flex-shrink-0 whitespace-nowrap status-badge-{{ $document->id }}"
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
                        <div class="flex items-center justify-between gap-3 mb-3 text-xs">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-1 text-gray-600">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>{{ $document->created_at->format('d M Y') }}</span>
                                </div>
                            </div>
                            <a href="{{ route('admin.download-document', $document) }}" class="text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1 whitespace-nowrap">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Download
                            </a>
                        </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Diupload</p>
                                        <p class="font-medium text-gray-900">{{ $document->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Terakhir Diubah</p>
                                        <p class="font-medium text-gray-900">{{ $document->updated_at->format('d M Y H:i') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Status</p>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold mt-1 status-badge-{{ $document->id }}"
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
                                    <div>
                                        <a href="{{ route('admin.download-document', $document) }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                            Download File
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Feedback Section -->
                        @if($document->feedback)
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 px-3 py-2 mb-3 rounded-r text-sm">
                            <p class="text-xs font-semibold text-yellow-900 mb-1 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                Feedback:
                            </p>
                            <p class="text-xs text-yellow-800">{{ $document->feedback }}</p>
                        </div>
                        @endif

                        <!-- Update Status Form -->
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-3.5 rounded-lg border border-gray-200">
                            <div class="grid grid-cols-1 sm:grid-cols-5 gap-2.5 items-end">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Status</label>
                                    <select name="documents[{{ $document->id }}][status]" class="w-full px-2.5 py-1.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent text-xs doc-status" data-doc-id="{{ $document->id }}" data-group="dokumen-lainnya">
                                        <option value="">Pilih</option>
                                        <option value="approved" {{ $document->status === 'approved' ? 'selected' : '' }}>✓ Setujui</option>
                                        <option value="revision" {{ $document->status === 'revision' ? 'selected' : '' }}>⟳ Revisi</option>
                                        <option value="rejected" {{ $document->status === 'rejected' ? 'selected' : '' }}>✕ Tolak</option>
                                    </select>
                                </div>
                                <div class="sm:col-span-4">
                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Catatan (Opsional)</label>
                                    <input type="text" name="documents[{{ $document->id }}][feedback]" placeholder="Masukkan catatan..." 
                                        value="{{ $document->feedback }}"
                                        class="w-full px-2.5 py-1.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent text-xs" data-group="dokumen-lainnya">
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Save Button for Section -->
            <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-green-100 border-t-2 border-green-200 flex justify-end">
                <button type="button" onclick="submitGroupForm('dokumen-lainnya')" class="bg-green-600 hover:bg-green-700 active:bg-green-800 text-white font-semibold py-2.5 px-6 rounded-lg transition shadow-md hover:shadow-lg inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Simpan Dokumen Lainnya</span>
                </button>
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
