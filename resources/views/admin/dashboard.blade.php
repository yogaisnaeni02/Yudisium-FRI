@extends('layouts.app')

@section('content')
<div class="container max-w-7xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard Admin Yudisium</h1>
        <p class="text-gray-600 mt-2">Verifikasi dan approval pengajuan yudisium mahasiswa</p>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Total Pengajuan</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_submissions'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-sm text-gray-600">Disetujui</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-sm text-gray-600">Sedang Review</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['under_review'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-600">Draft</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['draft'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
            <p class="text-sm text-gray-600">Ditolak</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</p>
        </div>
    </div>

    <!-- Submissions Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-blue-600">
            <h3 class="text-lg font-semibold text-white">Daftar Pengajuan Yudisium</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">NIM</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Nama Mahasiswa</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Progress</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Tanggal Submit</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($submissions as $submission)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $submission->student->nim }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $submission->student->user->name }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold"
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
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="w-16 bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $submission->getProgressPercentage() }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold">{{ $submission->getProgressPercentage() }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $submission->submitted_at ? $submission->submitted_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('admin.submission-detail', $submission) }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-600">
                                Tidak ada pengajuan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $submissions->links() }}
        </div>
    </div>
</div>
@endsection
