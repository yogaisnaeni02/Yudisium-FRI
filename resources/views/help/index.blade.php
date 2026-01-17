@extends('layouts.dashboard')

@section('title', 'Help & Support - SIYU')
@section('page-title', 'Help & Support')

@section('breadcrumb')
    <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('student.dashboard') }}"
       class="text-green-600 hover:text-green-700">Dashboard</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Help & Support</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    <!-- ================= HEADER ================= -->
    <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3
                      0 1.4-1.278 2.575-3.006 2.907
                      -.542.104-.994.54-.994 1.093m0 3h.01
                      M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 mb-2">Help & Support Center</h1>

        @if(auth()->user()->role === 'admin')
            <p class="text-gray-600">
                Panduan lengkap untuk administrator Sistem Informasi Yudisium (SIYU)
            </p>
        @else
            <p class="text-gray-600">
                Temukan jawaban dan panduan penggunaan Sistem Informasi Yudisium (SIYU)
            </p>
        @endif
    </div>

    <!-- ================= QUICK ACTIONS ================= -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white border rounded-lg p-4 text-center hover:shadow-md transition">
            <div class="w-12 h-12 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                          a1 1 0 01.707.293l5.414 5.414
                          a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="font-semibold">Panduan Lengkap</h3>
            <p class="text-sm text-gray-600">Pelajari semua fitur sistem</p>
        </div>

        <div class="bg-white border rounded-lg p-4 text-center hover:shadow-md transition">
            <div class="w-12 h-12 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8"/>
                </svg>
            </div>
            <h3 class="font-semibold">Hubungi Admin</h3>
            <p class="text-sm text-gray-600">Kirim pesan ke administrator</p>
        </div>

        <div class="bg-white border rounded-lg p-4 text-center hover:shadow-md transition">
            <div class="w-12 h-12 mx-auto bg-purple-100 rounded-full flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5"/>
                </svg>
            </div>
            <h3 class="font-semibold">Status Sistem</h3>
            <p class="text-sm text-gray-600">Monitoring & maintenance</p>
        </div>
    </div>

    <!-- ================= FAQ ================= -->
    <div class="bg-white border rounded-lg">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold">Pertanyaan Umum (FAQ)</h2>
        </div>

        <div class="divide-y">

            @if(auth()->user()->role === 'admin')
                @php
                    $faqs = [
                        'Bagaimana cara memverifikasi pengajuan yudisium mahasiswa?' => [
                            'Masuk ke menu Verifikasi Pengajuan',
                            'Periksa dokumen mahasiswa',
                            'Setujui atau tolak dengan alasan',
                            'Notifikasi terkirim otomatis'
                        ],
                        'Bagaimana cara mengelola periode yudisium?' => [
                            'Masuk menu Manajemen Periode',
                            'Tambah atau edit periode',
                            'Aktifkan periode',
                            'Pantau pengajuan'
                        ],
                    ];
                @endphp
            @else
                @php
                    $faqs = [
                        'Bagaimana cara mengajukan yudisium?' => [
                            'Buka menu Pengajuan Yudisium',
                            'Isi formulir',
                            'Upload dokumen',
                            'Tunggu verifikasi admin'
                        ],
                        'Apa yang harus dilakukan jika pengajuan ditolak?' => [
                            'Baca alasan penolakan',
                            'Perbaiki dokumen',
                            'Ajukan ulang'
                        ],
                    ];
                @endphp
            @endif

            @foreach($faqs as $question => $answers)
                <div class="p-6">
                    <button class="faq-toggle w-full flex justify-between items-center text-left">
                        <h3 class="font-semibold text-gray-900">{{ $question }}</h3>
                        <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-content hidden mt-4 text-gray-600">
                        <ol class="list-decimal list-inside space-y-1">
                            @foreach($answers as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ol>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- ================= SYSTEM STATUS ================= -->
    <div class="bg-white border rounded-lg p-6">
        <div class="flex items-center gap-2 mb-2">
            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
            <span class="font-semibold">Sistem Online</span>
        </div>
        <p class="text-sm text-gray-500">
            Terakhir diperiksa: {{ now()->format('d M Y, H:i') }} WIB
        </p>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.faq-toggle').forEach(button => {
    button.addEventListener('click', () => {
        const content = button.nextElementSibling;
        const icon = button.querySelector('svg');
        content.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    });
});
</script>
@endpush