@extends('layouts.dashboard')

@section('title', 'Edit Sidang Yudisium')
@section('page-title', 'Edit Data Sidang Yudisium')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-green-600 hover:text-green-700">Dashboard</a>
    <span class="mx-2 text-gray-400">/</span>
    <a href="{{ route('admin.yudisium-sidings') }}" class="text-green-600 hover:text-green-700">Sidang Yudisium</a>
    <span class="mx-2 text-gray-400">/</span>
    <a href="{{ route('admin.yudisium-sidings.show', $siding) }}" class="text-green-600 hover:text-green-700">Detail</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Edit</span>
@endsection

@section('content')
<div class="space-y-6">
    <form action="{{ route('admin.yudisium-sidings.update', $siding) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PATCH')

        <!-- Basic Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Informasi Dasar</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mahasiswa <span class="text-red-500">*</span></label>
                    <select name="student_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Pilih Mahasiswa</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ (old('student_id', $siding->student_id) == $student->id) ? 'selected' : '' }}>
                                {{ $student->nama }} ({{ $student->nim }})
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Periode <span class="text-red-500">*</span></label>
                    <select name="periode_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Pilih Periode</option>
                        @foreach($periodes as $periode)
                            <option value="{{ $periode->id }}" {{ (old('periode_id', $siding->periode_id) == $periode->id) ? 'selected' : '' }}>
                                {{ $periode->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('periode_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Sidang <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="tanggal_sidang" 
                        value="{{ old('tanggal_sidang', $siding->tanggal_sidang ? $siding->tanggal_sidang->format('Y-m-d\TH:i') : '') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    @error('tanggal_sidang')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Yudisium <span class="text-red-500">*</span></label>
                    <select name="status_yudisium" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="pending" {{ old('status_yudisium', $siding->status_yudisium) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="lulus" {{ old('status_yudisium', $siding->status_yudisium) == 'lulus' ? 'selected' : '' }}>Lulus</option>
                        <option value="tidak_lulus" {{ old('status_yudisium', $siding->status_yudisium) == 'tidak_lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                    </select>
                    @error('status_yudisium')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Dosen Wali -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Dosen Wali</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Dosen Wali</label>
                    <select name="dosen_wali_nama" id="dosen_wali_nama" class="w-full">
                        <option value="">Pilih Dosen Wali</option>
                        @foreach($dosens as $dosen)
                            <option value="{{ $dosen->nama_dosen }}" {{ old('dosen_wali_nama', $siding->dosen_wali_nama) == $dosen->nama_dosen ? 'selected' : '' }}>
                                {{ $dosen->nama_dosen }} - {{ $dosen->kode_dosen }} ({{ $dosen->prodi }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto Dosen Wali</label>
                    @if($siding->dosen_wali_foto)
                        <div class="mb-2">
                            <img src="{{ Storage::url($siding->dosen_wali_foto) }}" alt="Foto Dosen Wali" class="w-20 h-20 rounded-lg object-cover border border-gray-300">
                            <p class="text-xs text-gray-500 mt-1">Foto saat ini</p>
                        </div>
                    @endif
                    <input type="file" name="dosen_wali_foto" accept="image/*"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah foto</p>
                </div>
            </div>
        </div>

        <!-- Pembimbing -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Pembimbing</h2>
            <div class="space-y-6">
                <!-- Pembimbing 1 -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pembimbing 1</label>
                        <select name="pembimbing_1_nama" id="pembimbing_1_nama" class="w-full">
                            <option value="">Pilih Pembimbing 1</option>
                            @foreach($dosens as $dosen)
                                <option value="{{ $dosen->nama_dosen }}" {{ old('pembimbing_1_nama', $siding->pembimbing_1_nama) == $dosen->nama_dosen ? 'selected' : '' }}>
                                    {{ $dosen->nama_dosen }} - {{ $dosen->kode_dosen }} ({{ $dosen->prodi }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Pembimbing 1</label>
                        @if($siding->pembimbing_1_foto)
                            <div class="mb-2">
                                <img src="{{ Storage::url($siding->pembimbing_1_foto) }}" alt="Foto Pembimbing 1" class="w-20 h-20 rounded-lg object-cover border border-gray-300">
                                <p class="text-xs text-gray-500 mt-1">Foto saat ini</p>
                            </div>
                        @endif
                        <input type="file" name="pembimbing_1_foto" accept="image/*"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah foto</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nilai Pembimbing 1</label>
                        <input type="number" step="0.01" min="0" max="100" name="pembimbing_1_nilai" value="{{ old('pembimbing_1_nilai', $siding->pembimbing_1_nilai) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>
                <!-- Pembimbing 2 -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pembimbing 2</label>
                        <select name="pembimbing_2_nama" id="pembimbing_2_nama" class="w-full">
                            <option value="">Pilih Pembimbing 2</option>
                            @foreach($dosens as $dosen)
                                <option value="{{ $dosen->nama_dosen }}" {{ old('pembimbing_2_nama', $siding->pembimbing_2_nama) == $dosen->nama_dosen ? 'selected' : '' }}>
                                    {{ $dosen->nama_dosen }} - {{ $dosen->kode_dosen }} ({{ $dosen->prodi }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Pembimbing 2</label>
                        @if($siding->pembimbing_2_foto)
                            <div class="mb-2">
                                <img src="{{ Storage::url($siding->pembimbing_2_foto) }}" alt="Foto Pembimbing 2" class="w-20 h-20 rounded-lg object-cover border border-gray-300">
                                <p class="text-xs text-gray-500 mt-1">Foto saat ini</p>
                            </div>
                        @endif
                        <input type="file" name="pembimbing_2_foto" accept="image/*"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah foto</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nilai Pembimbing 2</label>
                        <input type="number" step="0.01" min="0" max="100" name="pembimbing_2_nilai" value="{{ old('pembimbing_2_nilai', $siding->pembimbing_2_nilai) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>
            </div>
        </div>

        <!-- Penguji -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Penguji</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Penguji Ketua</label>
                    <select name="penguji_ketua_nama" id="penguji_ketua_nama" class="w-full">
                        <option value="">Pilih Penguji Ketua</option>
                        @foreach($dosens as $dosen)
                            <option value="{{ $dosen->nama_dosen }}" {{ old('penguji_ketua_nama', $siding->penguji_ketua_nama) == $dosen->nama_dosen ? 'selected' : '' }}>
                                {{ $dosen->nama_dosen }} - {{ $dosen->kode_dosen }} ({{ $dosen->prodi }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto Penguji Ketua</label>
                    @if($siding->penguji_ketua_foto)
                        <div class="mb-2">
                            <img src="{{ Storage::url($siding->penguji_ketua_foto) }}" alt="Foto Penguji Ketua" class="w-20 h-20 rounded-lg object-cover border border-gray-300">
                            <p class="text-xs text-gray-500 mt-1">Foto saat ini</p>
                        </div>
                    @endif
                    <input type="file" name="penguji_ketua_foto" accept="image/*"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah foto</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nilai Penguji Ketua</label>
                    <input type="number" step="0.01" min="0" max="100" name="penguji_ketua_nilai" value="{{ old('penguji_ketua_nilai', $siding->penguji_ketua_nilai) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Penguji Anggota</label>
                    <select name="penguji_anggota_nama" id="penguji_anggota_nama" class="w-full">
                        <option value="">Pilih Penguji Anggota</option>
                        @foreach($dosens as $dosen)
                            <option value="{{ $dosen->nama_dosen }}" {{ old('penguji_anggota_nama', $siding->penguji_anggota_nama) == $dosen->nama_dosen ? 'selected' : '' }}>
                                {{ $dosen->nama_dosen }} - {{ $dosen->kode_dosen }} ({{ $dosen->prodi }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto Penguji Anggota</label>
                    @if($siding->penguji_anggota_foto)
                        <div class="mb-2">
                            <img src="{{ Storage::url($siding->penguji_anggota_foto) }}" alt="Foto Penguji Anggota" class="w-20 h-20 rounded-lg object-cover border border-gray-300">
                            <p class="text-xs text-gray-500 mt-1">Foto saat ini</p>
                        </div>
                    @endif
                    <input type="file" name="penguji_anggota_foto" accept="image/*"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah foto</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nilai Penguji Anggota</label>
                    <input type="number" step="0.01" min="0" max="100" name="penguji_anggota_nilai" value="{{ old('penguji_anggota_nilai', $siding->penguji_anggota_nilai) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
            </div>
        </div>

        <!-- Tugas Akhir -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Tugas Akhir</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul Tugas Akhir</label>
                    <textarea name="judul_tugas_akhir" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">{{ old('judul_tugas_akhir', $siding->judul_tugas_akhir) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Tugas Akhir</label>
                    <input type="text" name="jenis_tugas_akhir" value="{{ old('jenis_tugas_akhir', $siding->jenis_tugas_akhir) }}" placeholder="Contoh: Sidang"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
            </div>
        </div>

        <!-- Nilai dan Predikat -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Nilai dan Predikat</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nilai Total</label>
                    <input type="number" step="0.01" min="0" max="100" name="nilai_total" value="{{ old('nilai_total', $siding->nilai_total) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nilai Huruf</label>
                    <input type="text" name="nilai_huruf" value="{{ old('nilai_huruf', $siding->nilai_huruf) }}" placeholder="Contoh: AB, A, B"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Predikat Yudisium</label>
                    <input type="text" name="predikat_yudisium" value="{{ old('predikat_yudisium', $siding->predikat_yudisium) }}" placeholder="Contoh: SANGAT MEMUASKAN"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Cumlaude</label>
                    <select name="status_cumlaude" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Tidak</option>
                        <option value="cumlaude" {{ old('status_cumlaude', $siding->status_cumlaude) == 'cumlaude' ? 'selected' : '' }}>Cumlaude</option>
                        <option value="summa_cumlaude" {{ old('status_cumlaude', $siding->status_cumlaude) == 'summa_cumlaude' ? 'selected' : '' }}>Summa Cumlaude</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pemenuhan Jurnal/Publikasi/Lomba/dsb</label>
                    <textarea name="pemenuhan_jurnal" rows="2"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">{{ old('pemenuhan_jurnal', $siding->pemenuhan_jurnal) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Catatan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Catatan</h2>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                <textarea name="catatan" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">{{ old('catatan', $siding->catatan) }}</textarea>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end gap-4">
            <x-button variant="ghost" href="{{ route('admin.yudisium-sidings.show', $siding) }}">
                Batal
            </x-button>
            <x-button variant="primary" type="submit" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'>
                Update Data
            </x-button>
        </div>
    </form>
</div>

<!-- Tom Select CSS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<style>
.ts-wrapper.single .ts-control {
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    padding: 0.5rem 1rem;
    min-height: 42px;
}
.ts-wrapper.single .ts-control:focus {
    border-color: #10b981;
    outline: none;
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
}
.ts-dropdown {
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
.ts-dropdown .option {
    padding: 0.5rem 1rem;
}
.ts-dropdown .option.active {
    background-color: #10b981;
    color: white;
}
</style>
<!-- Tom Select JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Tom Select for all dosen dropdowns
    const dosenFields = [
        'dosen_wali_nama',
        'pembimbing_1_nama',
        'pembimbing_2_nama',
        'penguji_ketua_nama',
        'penguji_anggota_nama'
    ];

    dosenFields.forEach(fieldId => {
        const select = document.getElementById(fieldId);
        if (select) {
            new TomSelect(select, {
                placeholder: 'Cari atau pilih dosen...',
                allowEmptyOption: true,
                create: false,
                sortField: {
                    field: 'text',
                    direction: 'asc'
                },
                plugins: ['clear_button'],
                render: {
                    option: function(data, escape) {
                        return '<div class="flex items-center justify-between py-2">' +
                               '<span class="font-medium">' + escape(data.text) + '</span>' +
                               '</div>';
                    }
                }
            });
        }
    });
});
</script>
@endsection
