<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class YudisiumSiding extends Model
{
    protected $fillable = [
        'periode_id',
        'student_id',
        'tanggal_sidang',
        'predikat_yudisium',
        // Dosen Wali
        'dosen_wali_nama',
        'dosen_wali_foto',
        // Pembimbing 1
        'pembimbing_1_nama',
        'pembimbing_1_foto',
        'pembimbing_1_nilai',
        // Pembimbing 2
        'pembimbing_2_nama',
        'pembimbing_2_foto',
        'pembimbing_2_nilai',
        // Penguji Ketua
        'penguji_ketua_nama',
        'penguji_ketua_foto',
        'penguji_ketua_nilai',
        // Penguji Anggota
        'penguji_anggota_nama',
        'penguji_anggota_foto',
        'penguji_anggota_nilai',
        // Tugas Akhir
        'judul_tugas_akhir',
        'jenis_tugas_akhir',
        // Nilai
        'nilai_total',
        'nilai_huruf',
        // Status
        'status_cumlaude',
        'pemenuhan_jurnal',
        'status_yudisium',
        'catatan',
    ];

    protected $casts = [
        'tanggal_sidang' => 'datetime',
        'pembimbing_1_nilai' => 'decimal:2',
        'pembimbing_2_nilai' => 'decimal:2',
        'penguji_ketua_nilai' => 'decimal:2',
        'penguji_anggota_nilai' => 'decimal:2',
        'nilai_total' => 'decimal:2',
    ];

    /**
     * Get the student associated with the yudisium siding.
     */
    public function student(): BelongsTo
    {
        // Check which column exists in database
        // Cache the result to avoid repeated schema checks
        static $useStudentId = null;
        if ($useStudentId === null) {
            try {
                $columns = Schema::getColumnListing('yudisium_sidings');
                $useStudentId = in_array('student_id', $columns);
            } catch (\Exception $e) {
                $useStudentId = false;
            }
        }
        
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the periode associated with the yudisium siding.
     */
    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }
}
