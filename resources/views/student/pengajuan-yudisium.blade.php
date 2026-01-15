@extends('layouts.dashboard')

@section('title', 'Pengajuan Yudisium')
@section('page-title', 'Pengajuan Yudisium')

@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}" class="text-green-600 hover:text-green-700">Dashboard</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Pengajuan Yudisium</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Upload panel: sidebar + per-document sections -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                    @php
                        $groups = [
                            'Berkas Identitas & Akademik' => [
                                'Surat Pernyataan',
                                'Form Biodata Izajah & Transkip',
                                'KTP',
                                'Akta Lahir',
                                'Ijazah Pendidikan Terakhir',
                            ],
                            'Berkas Akademik & Karya' => [
                                'Buku TA yang Disahkan',
                                'Slide PPT',
                                'Screenshot (Gracias)',
                            ],
                            'Berkas Pendukung' => [
                                'Berkas Referensi (Minimal 10)',
                                'Bukti Approval Revisi (SOFI)',
                                'Bukti Approval SKPI',
                                'Surat Keterangan Bebas Pustaka (SKBP)',
                                'Dokumen Cumlaude (Publikasi/Lomba/HKI)',
                                'Dokumen Pendukung Tambahan',
                            ],
                        ];
                        $allDocs = array_merge(...array_values($groups));
                    @endphp
                <!-- Sidebar navigasi upload -->
                <aside class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow sticky top-8">
                        <div class="bg-gradient-to-r from-green-600 to-green-700 text-white p-4 rounded-t-lg">
                            <h4 class="font-semibold flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Upload Berkas
                            </h4>
                            <p class="text-sm text-green-100 mt-1">{{ $progress }}% Selesai</p>
                        </div>

                        <div class="p-4">
                            <!-- Progress Bar -->
                            <div class="mb-6">
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-green-500 h-3 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>

                            <!-- Document Groups (navigation per section) -->
                            <nav class="space-y-4">
                                @foreach($groups as $groupName => $docs)
                                    @php $groupSlug = \Illuminate\Support\Str::slug($groupName); @endphp
                                    <div>
                                        <button type="button" onclick="showSection('{{ $groupSlug }}')" data-section="{{ $groupSlug }}" class="w-full text-left p-3 rounded-lg hover:bg-blue-50 transition border-l-4 section-btn" style="border-left-color: #d1d5db;">
                                            <div class="flex items-center justify-between">
                                                <div class="text-sm font-semibold text-gray-900">{{ $groupName }}</div>
                                                <div class="text-xs text-gray-500">{{ count($docs) }} item</div>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">Klik untuk buka form upload grup berkas ini</p>
                                        </button>
                                    </div>
                                @endforeach
                            </nav>

                            <!-- Info Box -->
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <p class="text-xs font-bold text-blue-900 mb-2 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Catatan Penting
                                    </p>
                                    <ul class="text-xs text-blue-800 space-y-1">
                                        <li>• Pastikan semua berkas lengkap</li>
                                        <li>• Format: PDF, DOC, DOCX</li>
                                        <li>• Maksimal 5MB per file</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Main content: per-section bulk upload -->
                <div class="lg:col-span-4">
                    @foreach($groups as $groupName => $docs)
                        @php $groupSlug = \Illuminate\Support\Str::slug($groupName); @endphp
                        <section class="section-content bg-white rounded-lg shadow mb-6 overflow-hidden transition-all duration-300 {{ $loop->first ? '' : 'hidden' }}" data-section="{{ $groupSlug }}" id="group-{{ $groupSlug }}">
                            <div class="bg-gradient-to-r from-gray-50 to-white p-6 border-b border-gray-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="text-xl font-bold text-gray-900">{{ $groupName }}</h4>
                                        <p class="text-sm text-gray-600 mt-2">Unggah semua berkas yang diperlukan untuk seksi ini. Anda bisa memilih beberapa file untuk tiap item jika diperlukan.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 space-y-6">
                                <form onsubmit="uploadSection(event, '{{ $groupSlug }}')" enctype="multipart/form-data" class="space-y-6" data-group-form>
                                    @csrf
                                    <input type="hidden" name="group_name" value="{{ $groupName }}">

                                    <!-- Combined Upload Section with Status -->
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                            </svg>
                                            Unggah atau Ganti Berkas
                                        </p>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @php $allDisabled = true; @endphp
                                            @foreach($docs as $type)
                                                @php
                                                    $slug = \Illuminate\Support\Str::slug($type);
                                                    $doc = $documents->where('type', $type)->first();
                                                    $inputDisabled = $doc && ($doc->status === 'pending' || $doc->status === 'approved');
                                                    if (!$inputDisabled) { $allDisabled = false; }
                                                @endphp
                                                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                                    <div class="flex items-center justify-between mb-3">
                                                        <p class="text-sm font-semibold text-gray-900">{{ $type }}</p>
                                                        @if($doc && $submission->status === 'draft')
                                                            <button type="button" onclick="toggleReplaceForm('{{ $slug }}')" id="replace-toggle-{{ $slug }}" class="text-xs text-blue-600 hover:text-blue-800">
                                                                [Ganti File]
                                                            </button>
                                                        @endif
                                                    </div>

                                                    <!-- Show uploaded document status if exists -->
                                                    @if($doc)
                                                        <div class="mb-3 p-3 bg-white rounded-lg border border-gray-200">
                                                            <div class="flex items-center justify-between">
                                                                <div class="flex items-center gap-2">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                                    </svg>
                                                                    <div>
                                                                        <p class="text-xs font-medium text-gray-900">{{ $doc->name }}</p>
                                                                        <p class="text-xs text-gray-500">Diupload: {{ $doc->created_at->format('d M Y H:i') }}</p>
                                                                    </div>
                                                                </div>
                                                                <span class="text-xs px-2 py-1 rounded"
                                                                    @switch($doc->status)
                                                                        @case('approved') style="background-color: #dcfce7; color: #15803d;" @break
                                                                        @case('pending') style="background-color: #f3f4f6; color: #374151;" @break
                                                                        @case('revision') style="background-color: #fef3c7; color: #92400e;" @break
                                                                        @case('rejected') style="background-color: #fee2e2; color: #b91c1c;" @break
                                                                    @endswitch
                                                                >
                                                                    @switch($doc->status)
                                                                        @case('approved')
                                                                            <span class="flex items-center gap-1">
                                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                                </svg>
                                                                                Disetujui
                                                                            </span>
                                                                            @break
                                                                        @case('pending')
                                                                            <span class="flex items-center gap-1">
                                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                                </svg>
                                                                                Menunggu Review
                                                                            </span>
                                                                            @break
                                                                        @case('revision')
                                                                            <span class="flex items-center gap-1">
                                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                                                </svg>
                                                                                Perlu Revisi
                                                                            </span>
                                                                            @break
                                                                        @case('rejected')
                                                                            <span class="flex items-center gap-1">
                                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                                </svg>
                                                                                Ditolak
                                                                            </span>
                                                                            @break
                                                                    @endswitch
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <p class="text-xs text-gray-500 mb-3">Format: PDF/DOC/DOCX | Maks. 5MB per file</p>

                                                    <!-- Hidden by default if document exists and not replacing -->
                                                    <div id="upload-form-{{ $slug }}" style="display: {{ ($doc && $submission->status === 'draft' && $doc->status === 'rejected') ? 'none' : 'block' }};">
                                                        <label class="block text-xs font-medium text-gray-700 mb-2">Pilih File (boleh banyak)</label>
                                                        <input type="file" name="files[{{ $slug }}][]" multiple accept=".pdf,.doc,.docx" class="w-full text-sm text-gray-700" onchange="validateAndPreviewFiles(this, '{{ $slug }}')" @if($inputDisabled) disabled @endif>
                                                        <!-- File preview & validation warnings -->
                                                        <div class="mt-2 file-preview-{{ $slug }}" style="display: none;">
                                                            <div class="text-xs font-semibold text-gray-700 mb-2">File yang dipilih:</div>
                                                            <ul class="file-list-{{ $slug }} space-y-1 text-xs"></ul>
                                                            <div class="file-warnings-{{ $slug }} mt-2 space-y-1"></div>
                                                        </div>
                                                    </div>

                                                    <!-- Replace form (shown by default for rejected) -->
                                                    @if($doc && $submission->status === 'draft' && $doc->status === 'rejected')
                                                        <div id="replace-form-{{ $slug }}" style="display: block;" class="space-y-2">
                                                            <p class="text-xs text-orange-700 mb-2">Pilih file baru untuk mengganti <strong>{{ $doc->name }}</strong></p>
                                                            <input type="file" name="files[{{ $slug }}][]" accept=".pdf,.doc,.docx" class="w-full text-sm text-gray-700" onchange="validateAndPreviewFiles(this, '{{ $slug }}')">
                                                            <div class="mt-2 file-preview-{{ $slug }}" style="display: none;">
                                                                <div class="text-xs font-semibold text-gray-700 mb-2">File yang dipilih:</div>
                                                                <ul class="file-list-{{ $slug }} space-y-1 text-xs"></ul>
                                                                <div class="file-warnings-{{ $slug }} mt-2 space-y-1"></div>
                                                            </div>
                                                            <p class="text-xs text-gray-600 mt-2">Upload bersama file lain menggunakan tombol di bawah</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-6 rounded-lg transition flex items-center gap-2" @if($allDisabled) disabled @endif>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Unggah Semua Berkas Seksi Ini
                                        </button>
                                        <button type="button" onclick="document.querySelector('#group-{{ $groupSlug }} [data-group-form]').reset()" class="text-sm text-gray-600">Reset form</button>
                                    </div>
                                </form>
                            </div>
                        </section>
                    @endforeach
                </div>
            </div>

    <!-- Submit Button (if status is draft) -->
    @if($submission->status === 'draft' && $documents->count() > 0)
    <div class="mt-8">
        <form action="{{ route('student.submit-application') }}" method="POST">
            @csrf
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-8 rounded-lg transition shadow-lg hover:shadow-xl flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Kirim Pengajuan Yudisium
            </button>
        </form>
    </div>
    @endif
</div>

<!-- Upload JS handlers -->
<script>
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    const ALLOWED_TYPES = ['pdf', 'doc', 'docx'];

    function toggleReplaceForm(slug) {
        const uploadForm = document.querySelector(`#upload-form-${slug}`);
        const replaceForm = document.querySelector(`#replace-form-${slug}`);
        const toggle = document.querySelector(`#replace-toggle-${slug}`);
        
        if (uploadForm && replaceForm) {
            const uploadVisible = uploadForm.style.display !== 'none';
            uploadForm.style.display = uploadVisible ? 'none' : 'block';
            replaceForm.style.display = uploadVisible ? 'block' : 'none';
            
            // Clear preview when toggling
            const previewDiv = document.querySelector(`.file-preview-${slug}`);
            if (previewDiv) previewDiv.style.display = 'none';
        }
    }

    function validateAndPreviewFiles(inputElement, slug) {
        const files = Array.from(inputElement.files);
        const previewDiv = document.querySelector(`.file-preview-${slug}`);
        const fileListDiv = document.querySelector(`.file-list-${slug}`);
        const warningsDiv = document.querySelector(`.file-warnings-${slug}`);

        fileListDiv.innerHTML = '';
        warningsDiv.innerHTML = '';

        if (files.length === 0) {
            previewDiv.style.display = 'none';
            return;
        }

        previewDiv.style.display = 'block';
        let hasWarnings = false;

        files.forEach((file, idx) => {
            const fileExt = file.name.split('.').pop().toLowerCase();
            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
            
            let statusIcon = '<svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
            let isValid = true;

            // Check type
            if (!ALLOWED_TYPES.includes(fileExt)) {
                statusIcon = '<svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
                isValid = false;
                hasWarnings = true;
            }
            // Check size
            else if (file.size > MAX_FILE_SIZE) {
                statusIcon = '<svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';
                isValid = false;
                hasWarnings = true;
            }

            const fileItem = document.createElement('li');
            fileItem.className = 'flex items-center gap-2 p-2 bg-gray-100 rounded';
            fileItem.innerHTML = `
                <span>${statusIcon}</span>
                <span class="flex-1 truncate">${file.name}</span>
                <span class="text-gray-500">${fileSizeMB}MB</span>
            `;
            fileListDiv.appendChild(fileItem);

            // Add warnings
            if (!isValid) {
                const warning = document.createElement('div');
                warning.className = 'p-2 bg-yellow-50 border border-yellow-200 rounded text-yellow-800';
                let msg = `<strong>${file.name}:</strong> `;
                
                if (!ALLOWED_TYPES.includes(fileExt)) {
                    msg += `Format tidak diterima (hanya PDF/DOC/DOCX)`;
                } else if (file.size > MAX_FILE_SIZE) {
                    msg += `Ukuran file melebihi 5MB`;
                }
                
                warning.innerHTML = msg;
                warningsDiv.appendChild(warning);
            }
        });

        // Show summary
        if (files.length > 0) {
            const summary = document.createElement('div');
            summary.className = `mt-2 p-2 rounded text-xs ${hasWarnings ? 'bg-yellow-50 text-yellow-700' : 'bg-green-50 text-green-700'}`;
            summary.innerHTML = `${files.length} file dipilih ${hasWarnings ? '(ada peringatan)' : '(siap diunggah)'}`;
            warningsDiv.appendChild(summary);
        }
    }

    function uploadSingleDocument(event, slug, groupSlug) {
        const fileInput = event.target.closest('div').querySelector('input[type="file"]');
        if (!fileInput || fileInput.files.length === 0) {
            alert('Pilih file terlebih dahulu');
            return;
        }

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('group_name', document.querySelector(`#group-${groupSlug} input[name="group_name"]`).value);

        // Add only the files for this specific document type
        Array.from(fileInput.files).forEach(file => {
            formData.append(`files[${slug}][]`, file);
        });

        const btn = event.target;
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Mengunggah...';

        fetch('{{ route("student.upload-document") }}', {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' }
        })
        .then(res => {
            console.log('Response status:', res.status);
            if (!res.ok && res.status !== 422) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                const alert = document.createElement('div');
                alert.className = 'p-4 mb-4 bg-green-50 border border-green-200 rounded-lg';
                const count = data.documents ? data.documents.length : 0;
                alert.innerHTML = `<p class="text-green-700 flex items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><strong>Sukses!</strong> ${count} file berhasil diunggah.</p>`;
                const section = document.querySelector(`#group-${groupSlug} .p-6`);
                section.insertBefore(alert, section.firstChild);
                setTimeout(() => alert.remove(), 4000);

                // Reset this specific input and preview
                fileInput.value = '';
                const previewDiv = document.querySelector(`.file-preview-${slug}`);
                if (previewDiv) previewDiv.style.display = 'none';
            } else {
                const alert = document.createElement('div');
                alert.className = 'p-4 mb-4 bg-red-50 border border-red-200 rounded-lg';
                alert.innerHTML = `<p class="text-red-700 flex items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg><strong>Gagal!</strong> ${data.message || 'Unggah gagal'}</p>`;
                const section = document.querySelector(`#group-${groupSlug} .p-6`);
                section.insertBefore(alert, section.firstChild);
                setTimeout(() => alert.remove(), 5000);
            }
        })
        .catch(err => {
            const alert = document.createElement('div');
            alert.className = 'p-4 mb-4 bg-red-50 border border-red-200 rounded-lg';
            alert.innerHTML = `<p class="text-red-700 flex items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg><strong>Error:</strong> ${err.message}</p>`;
            const section = document.querySelector(`#group-${groupSlug} .p-6`);
            section.insertBefore(alert, section.firstChild);
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = originalText;
        });
    }

    function uploadSection(e, groupSlug) {
        e.preventDefault();
        const form = e.target;

        // Collect all files and check if any exist
        const fileInputs = form.querySelectorAll('input[type="file"]');
        let totalFiles = 0;
        fileInputs.forEach(input => {
            totalFiles += input.files.length;
        });

        console.log('Starting upload for group:', groupSlug);
        console.log('Total files found:', totalFiles);

        if (totalFiles === 0) {
            alert('Pilih minimal 1 file untuk diunggah');
            return;
        }

        const formData = new FormData(form);
        console.log('FormData entries:');
        for (let [key, value] of formData.entries()) {
            if (value instanceof File) {
                console.log(key, ': File -', value.name, '(', value.size, 'bytes)');
            } else {
                console.log(key, ':', value);
            }
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Mengunggah...';

        console.log('Sending request to:', '{{ route("student.upload-document") }}');

        fetch('{{ route("student.upload-document") }}', {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' }
        })
        .then(res => {
            console.log('Response status:', res.status);
            console.log('Response headers:', res.headers);
            if (!res.ok && res.status !== 422) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                const alert = document.createElement('div');
                alert.className = 'p-4 mb-4 bg-green-50 border border-green-200 rounded-lg';
                const count = data.documents ? data.documents.length : 0;
                alert.innerHTML = `<p class="text-green-700 flex items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><strong>Sukses!</strong> ${count} file berhasil diunggah. Halaman akan dimuat ulang...</p>`;
                form.parentNode.insertBefore(alert, form);

                console.log('Upload successful, reloading page...');

                // Force reload from server to avoid cache issues
                window.location.reload(true);
            } else {
                const alert = document.createElement('div');
                alert.className = 'p-4 mb-4 bg-red-50 border border-red-200 rounded-lg';
                alert.innerHTML = `<p class="text-red-700 flex items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg><strong>Gagal!</strong> ${data.message || 'Unggah gagal'}</p>`;
                form.parentNode.insertBefore(alert, form);
                setTimeout(() => alert.remove(), 5000);
            }
        })
        .catch(err => {
            console.error('Upload error:', err);
            const alert = document.createElement('div');
            alert.className = 'p-4 mb-4 bg-red-50 border border-red-200 rounded-lg';
            alert.innerHTML = `<p class="text-red-700 flex items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg><strong>Error:</strong> ${err.message}</p>`;
            form.parentNode.insertBefore(alert, form);
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    }

    function showSection(slug) {
        // hide all sections
        document.querySelectorAll('.section-content').forEach(s => s.classList.add('hidden'));
        // remove active style from all buttons
        document.querySelectorAll('.section-btn').forEach(b => b.classList.remove('ring-2', 'ring-blue-400'));
        // target section element specifically (avoid matching nav button)
        const sec = document.querySelector(`.section-content[data-section="${slug}"]`);
        if (sec) {
            sec.classList.remove('hidden');
            sec.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            console.debug('Section not found for', slug);
        }
        // target nav button specifically
        const btn = document.querySelector(`.section-btn[data-section="${slug}"]`);
        if (btn) btn.classList.add('ring-2', 'ring-blue-400');
    }

    window.addEventListener('load', () => {
        const firstBtn = document.querySelector('.section-btn');
        if (firstBtn) firstBtn.click();
    });
</script>
@endsection
