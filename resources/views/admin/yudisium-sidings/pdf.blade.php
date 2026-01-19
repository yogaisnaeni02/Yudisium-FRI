<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidang Yudisium - {{ $siding->student->nama }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            size: A4;
            margin: 10mm;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 8px;
            color: #1f2937;
            line-height: 1.2;
            padding: 0;
        }
        
        .header {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            padding: 8px 12px;
            text-align: center;
            border-radius: 4px;
            margin-bottom: 6px;
        }
        
        .header h1 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .header p {
            font-size: 7px;
            margin: 1px 0;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 7px;
            margin-top: 3px;
        }
        
        .status-lulus {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .status-tidak-lulus {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .main-grid {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        
        .left-col {
            display: table-cell;
            width: 55%;
            vertical-align: top;
            padding-right: 6px;
        }
        
        .right-col {
            display: table-cell;
            width: 45%;
            vertical-align: top;
        }
        
        .section {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 6px;
            margin-bottom: 5px;
        }
        
        .section-title {
            font-size: 9px;
            font-weight: bold;
            color: #059669;
            margin-bottom: 4px;
            padding-bottom: 3px;
            border-bottom: 1.5px solid #059669;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            font-weight: 600;
            color: #6b7280;
            padding: 2px 0;
            width: 90px;
            font-size: 7px;
        }
        
        .info-value {
            display: table-cell;
            color: #1f2937;
            padding: 2px 0;
            font-size: 7px;
        }
        
        .student-photo {
            width: 50px;
            height: 65px;
            border: 1.5px solid #d1d5db;
            border-radius: 4px;
            background-color: #f9fafb;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 4px;
        }
        
        .academic-row {
            display: table;
            width: 100%;
            margin-top: 4px;
        }
        
        .academic-cell {
            display: table-cell;
            text-align: center;
            padding: 3px;
            border-right: 1px solid #e5e7eb;
            vertical-align: top;
        }
        
        .academic-cell:last-child {
            border-right: none;
        }
        
        .dosen-wali-mini {
            background: #eff6ff;
            border: 1px solid #93c5fd;
            border-radius: 3px;
            padding: 3px;
        }
        
        .dosen-photo-mini {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            margin: 0 auto 2px;
            border: 1px solid #3b82f6;
            background-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .dosen-name-mini {
            font-size: 6px;
            font-weight: 600;
            color: #1e40af;
            margin-bottom: 1px;
        }
        
        .dosen-nip-mini {
            font-size: 5px;
            color: #3b82f6;
        }
        
        .academic-card {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 3px;
            padding: 4px;
        }
        
        .academic-label {
            font-size: 6px;
            color: #166534;
            margin-bottom: 2px;
            font-weight: 600;
        }
        
        .academic-value {
            font-size: 12px;
            font-weight: bold;
            color: #059669;
        }
        
        .grades-row {
            display: table;
            width: 100%;
            margin-top: 4px;
        }
        
        .grade-cell {
            display: table-cell;
            padding: 3px;
            border: 1px solid #e5e7eb;
            border-radius: 3px;
            margin-right: 3px;
            vertical-align: top;
            width: 25%;
            text-align: center;
        }
        
        .grade-photo {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            margin: 0 auto 2px;
            border: 1px solid #d1d5db;
            background-color: #f9fafb;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .grade-label {
            font-size: 6px;
            color: #6b7280;
            margin-bottom: 1px;
            font-weight: 600;
        }
        
        .grade-name {
            font-size: 6px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1px;
            word-wrap: break-word;
        }
        
        .grade-nip {
            font-size: 5px;
            color: #9ca3af;
        }
        
        .grade-score {
            font-size: 14px;
            font-weight: bold;
            color: #059669;
            margin-top: 2px;
        }
        
        .total-grade-box {
            background: #ecfdf5;
            border: 2px solid #10b981;
            border-radius: 4px;
            padding: 6px;
            text-align: center;
            margin-top: 5px;
        }
        
        .total-grade-label {
            font-size: 7px;
            color: #166534;
            margin-bottom: 2px;
            font-weight: 600;
        }
        
        .total-grade-value {
            font-size: 20px;
            font-weight: bold;
            color: #059669;
        }
        
        .total-grade-letter {
            font-size: 14px;
            color: #047857;
            margin-left: 4px;
        }
        
        .bottom-row {
            display: table;
            width: 100%;
            margin-top: 5px;
        }
        
        .bottom-cell {
            display: table-cell;
            vertical-align: top;
            padding-right: 5px;
            width: 50%;
        }
        
        .bottom-cell:last-child {
            padding-right: 0;
        }
        
        .predikat-box {
            background: #fef3c7;
            border: 1.5px solid #f59e0b;
            border-radius: 4px;
            padding: 5px;
        }
        
        .predikat-label {
            font-size: 7px;
            color: #92400e;
            margin-bottom: 2px;
            font-weight: 600;
        }
        
        .predikat-value {
            font-size: 11px;
            font-weight: bold;
            color: #78350f;
            margin-bottom: 3px;
        }
        
        .status-box {
            background: #f0f9ff;
            border: 1.5px solid #0ea5e9;
            border-radius: 4px;
            padding: 5px;
        }
        
        .status-label {
            font-size: 7px;
            color: #0c4a6e;
            margin-bottom: 2px;
            font-weight: 600;
        }
        
        .status-value {
            font-size: 10px;
            font-weight: bold;
            color: #0369a1;
            margin-bottom: 3px;
        }
        
        .info-box {
            background: rgba(255,255,255,0.6);
            border: 1px solid #e5e7eb;
            border-radius: 3px;
            padding: 3px;
            margin-top: 3px;
        }
        
        .info-box-label {
            font-size: 6px;
            color: #6b7280;
            margin-bottom: 1px;
            font-weight: 600;
        }
        
        .info-box-value {
            font-size: 6px;
            color: #1f2937;
        }
        
        .footer {
            margin-top: 5px;
            padding-top: 4px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 6px;
            color: #6b7280;
        }
        
        .highlight {
            color: #059669;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Sidang Yudisium</h1>
        <p>Fakultas Rekayasa Industri - Telkom University</p>
        <p>
            {{ $siding->periode->nama ?? 'Periode Tidak Ditetapkan' }} - 
            Tahun {{ $siding->tanggal_sidang ? $siding->tanggal_sidang->format('Y') : date('Y') }}
        </p>
        <div class="status-badge 
            @if($siding->status_yudisium === 'lulus') status-lulus
            @elseif($siding->status_yudisium === 'tidak_lulus') status-tidak-lulus
            @else status-pending
            @endif">
            @if($siding->status_yudisium === 'lulus')
                Lulus
            @elseif($siding->status_yudisium === 'tidak_lulus')
                Tidak Lulus
            @else
                Pending
            @endif
        </div>
    </div>

    <!-- Main Grid -->
    <div class="main-grid">
        <div class="left-col">
            <!-- Student Info -->
            <div class="section">
                <h2 class="section-title">Informasi Mahasiswa</h2>
                <div style="display: table; width: 100%;">
                    <div class="info-row">
                        <div class="info-label">Nama</div>
                        <div class="info-value highlight">{{ $siding->student->nama }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">NIM</div>
                        <div class="info-value highlight">{{ $siding->student->nim }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Program Studi</div>
                        <div class="info-value">{{ $siding->student->prodi }}</div>
                    </div>
                    @if($siding->judul_tugas_akhir)
                    <div class="info-row">
                        <div class="info-label">Judul TA</div>
                        <div class="info-value" style="font-size: 6px;">{{ mb_strlen($siding->judul_tugas_akhir) > 45 ? mb_substr($siding->judul_tugas_akhir, 0, 45) . '...' : $siding->judul_tugas_akhir }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Academic Summary -->
            <div class="section">
                <h2 class="section-title">Ringkasan Akademik</h2>
                <div class="academic-row">
                    <div class="academic-cell">
                        <div class="dosen-wali-mini">
                            <div class="dosen-photo-mini">
                                @if(isset($imagePaths['dosen_wali']))
                                    <img src="{{ $imagePaths['dosen_wali'] }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                @else
                                    <span style="font-size: 12px;">👤</span>
                                @endif
                            </div>
                            <div class="dosen-name-mini">Dosen Wali</div>
                            <div class="dosen-name-mini" style="font-size: 5px;">{{ mb_strlen($siding->dosen_wali_nama ?? '-') > 15 ? mb_substr($siding->dosen_wali_nama ?? '-', 0, 15) . '...' : ($siding->dosen_wali_nama ?? '-') }}</div>
                            @if(isset($dosens['dosen_wali']) && $dosens['dosen_wali'])
                                <div class="dosen-nip-mini">NIP: {{ $dosens['dosen_wali']->nip }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="academic-cell">
                        <div class="academic-card">
                            <div class="academic-label">IPK</div>
                            <div class="academic-value">{{ number_format($siding->student->ipk ?? 0, 2) }}</div>
                        </div>
                    </div>
                    <div class="academic-cell">
                        <div class="academic-card">
                            <div class="academic-label">EPRT</div>
                            <div class="academic-value">{{ $siding->student->total_sks ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="academic-cell">
                        <div class="academic-card">
                            <div class="academic-label">TAK</div>
                            <div class="academic-value">{{ $siding->student->tak ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="academic-cell">
                        <div class="academic-card">
                            <div class="academic-label">IKK</div>
                            <div class="academic-value">{{ number_format(($siding->student->tak ?? 0) / 120 * 4, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grades -->
            <div class="section">
                <h2 class="section-title">Nilai Sidang</h2>
                <div class="grades-row">
                    <div class="grade-cell">
                        <div class="grade-photo">
                            @if(isset($imagePaths['pembimbing_1']))
                                <img src="{{ $imagePaths['pembimbing_1'] }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            @else
                                <span style="font-size: 10px;">👤</span>
                            @endif
                        </div>
                        <div class="grade-label">Pembimbing 1</div>
                        <div class="grade-name">{{ mb_strlen($siding->pembimbing_1_nama ?? '-') > 18 ? mb_substr($siding->pembimbing_1_nama ?? '-', 0, 18) . '...' : ($siding->pembimbing_1_nama ?? '-') }}</div>
                        @if(isset($dosens['pembimbing_1']) && $dosens['pembimbing_1'])
                            <div class="grade-nip">NIP: {{ $dosens['pembimbing_1']->nip }}</div>
                        @endif
                        <div class="grade-score">{{ $siding->pembimbing_1_nilai ? number_format($siding->pembimbing_1_nilai, 2) : '-' }}</div>
                    </div>
                    <div class="grade-cell">
                        <div class="grade-photo">
                            @if(isset($imagePaths['pembimbing_2']))
                                <img src="{{ $imagePaths['pembimbing_2'] }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            @else
                                <span style="font-size: 10px;">👤</span>
                            @endif
                        </div>
                        <div class="grade-label">Pembimbing 2</div>
                        <div class="grade-name">{{ mb_strlen($siding->pembimbing_2_nama ?? '-') > 18 ? mb_substr($siding->pembimbing_2_nama ?? '-', 0, 18) . '...' : ($siding->pembimbing_2_nama ?? '-') }}</div>
                        @if(isset($dosens['pembimbing_2']) && $dosens['pembimbing_2'])
                            <div class="grade-nip">NIP: {{ $dosens['pembimbing_2']->nip }}</div>
                        @endif
                        <div class="grade-score">{{ $siding->pembimbing_2_nilai ? number_format($siding->pembimbing_2_nilai, 2) : '-' }}</div>
                    </div>
                    <div class="grade-cell">
                        <div class="grade-photo">
                            @if(isset($imagePaths['penguji_ketua']))
                                <img src="{{ $imagePaths['penguji_ketua'] }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            @else
                                <span style="font-size: 10px;">👤</span>
                            @endif
                        </div>
                        <div class="grade-label">Penguji Ketua</div>
                        <div class="grade-name">{{ mb_strlen($siding->penguji_ketua_nama ?? '-') > 18 ? mb_substr($siding->penguji_ketua_nama ?? '-', 0, 18) . '...' : ($siding->penguji_ketua_nama ?? '-') }}</div>
                        @if(isset($dosens['penguji_ketua']) && $dosens['penguji_ketua'])
                            <div class="grade-nip">NIP: {{ $dosens['penguji_ketua']->nip }}</div>
                        @endif
                        <div class="grade-score">{{ $siding->penguji_ketua_nilai ? number_format($siding->penguji_ketua_nilai, 2) : '-' }}</div>
                    </div>
                    <div class="grade-cell">
                        <div class="grade-photo">
                            @if(isset($imagePaths['penguji_anggota']))
                                <img src="{{ $imagePaths['penguji_anggota'] }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            @else
                                <span style="font-size: 10px;">👤</span>
                            @endif
                        </div>
                        <div class="grade-label">Penguji Anggota</div>
                        <div class="grade-name">{{ mb_strlen($siding->penguji_anggota_nama ?? '-') > 18 ? mb_substr($siding->penguji_anggota_nama ?? '-', 0, 18) . '...' : ($siding->penguji_anggota_nama ?? '-') }}</div>
                        @if(isset($dosens['penguji_anggota']) && $dosens['penguji_anggota'])
                            <div class="grade-nip">NIP: {{ $dosens['penguji_anggota']->nip }}</div>
                        @endif
                        <div class="grade-score">{{ $siding->penguji_anggota_nilai ? number_format($siding->penguji_anggota_nilai, 2) : '-' }}</div>
                    </div>
                </div>
                
                <div class="total-grade-box">
                    <div class="total-grade-label">Nilai Total</div>
                    <div class="total-grade-value">
                        {{ $siding->nilai_total ? number_format($siding->nilai_total, 2) : '-' }}
                        @if($siding->nilai_huruf)
                            <span class="total-grade-letter">({{ $siding->nilai_huruf }})</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="right-col">
            <!-- Student Photo -->
            <div class="section" style="text-align: center;">
                <h2 class="section-title">Foto Mahasiswa</h2>
                <div class="student-photo">
                    @if(isset($imagePaths['student']))
                        <img src="{{ $imagePaths['student'] }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 3px;">
                    @else
                        <span style="font-size: 10px; color: #9ca3af;">📷</span>
                    @endif
                </div>
            </div>

            <!-- Predikat & Status -->
            <div class="section predikat-box">
                <h2 class="section-title" style="color: #92400e; border-color: #f59e0b;">Predikat Yudisium</h2>
                <div class="predikat-value">{{ $siding->predikat_yudisium ?? '-' }}</div>
                
                <div class="info-box">
                    <div class="info-box-label">Status Cumlaude</div>
                    <div class="info-box-value" style="font-weight: bold;">
                        @if($siding->status_cumlaude === 'cumlaude')
                            Cumlaude
                        @elseif($siding->status_cumlaude === 'summa_cumlaude')
                            Summa Cumlaude
                        @else
                            -
                        @endif
                    </div>
                </div>
                
                @if($siding->pemenuhan_jurnal)
                <div class="info-box">
                    <div class="info-box-label">Pemenuhan Jurnal</div>
                    <div class="info-box-value">{{ mb_strlen($siding->pemenuhan_jurnal) > 35 ? mb_substr($siding->pemenuhan_jurnal, 0, 35) . '...' : $siding->pemenuhan_jurnal }}</div>
                </div>
                @endif
            </div>
            
            <div class="section status-box">
                <h2 class="section-title" style="color: #0c4a6e; border-color: #0ea5e9;">Status Yudisium</h2>
                <div class="status-value">
                    @if($siding->status_yudisium === 'lulus')
                        Lulus Yudisium
                    @elseif($siding->status_yudisium === 'tidak_lulus')
                        Tidak Lulus
                    @else
                        Pending
                    @endif
                </div>
                
                <div class="info-box">
                    <div class="info-box-label">Tanggal Sidang</div>
                    <div class="info-box-value" style="font-weight: bold;">
                        {{ $siding->tanggal_sidang ? $siding->tanggal_sidang->format('d M Y, H:i') : '-' }}
                    </div>
                </div>
                
                @if($siding->catatan)
                <div class="info-box">
                    <div class="info-box-label">Catatan</div>
                    <div class="info-box-value">{{ mb_strlen($siding->catatan) > 40 ? mb_substr($siding->catatan, 0, 40) . '...' : $siding->catatan }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Sistem Informasi Yudisium</strong> | Fakultas Rekayasa Industri - Telkom University</p>
        <p>Dokumen dihasilkan otomatis pada {{ date('d M Y, H:i:s') }}</p>
    </div>
</body>
</html>
