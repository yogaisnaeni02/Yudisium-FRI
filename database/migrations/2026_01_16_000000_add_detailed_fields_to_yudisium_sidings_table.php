<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add student_id column if it doesn't exist
        if (!Schema::hasColumn('yudisium_sidings', 'student_id')) {
            $hasMahasiswaId = Schema::hasColumn('yudisium_sidings', 'mahasiswa_id');
            
            Schema::table('yudisium_sidings', function (Blueprint $table) use ($hasMahasiswaId) {
                if ($hasMahasiswaId) {
                    // If mahasiswa_id exists, add student_id as nullable first
                    $table->unsignedBigInteger('student_id')->nullable()->after('periode_id');
                } else {
                    // If mahasiswa_id doesn't exist, add student_id directly
                    $table->unsignedBigInteger('student_id')->after('periode_id');
                }
            });
            
            // Step 2: Copy data from mahasiswa_id to student_id (after column is created)
            if ($hasMahasiswaId) {
                DB::statement('UPDATE yudisium_sidings SET student_id = mahasiswa_id WHERE student_id IS NULL');
                
                // Step 3: Make student_id not nullable and add foreign key constraint
                Schema::table('yudisium_sidings', function (Blueprint $table) {
                    $table->unsignedBigInteger('student_id')->nullable(false)->change();
                });
            }
            
            // Step 4: Add foreign key constraint
            Schema::table('yudisium_sidings', function (Blueprint $table) {
                $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            });
        }
        
        // Step 5: Add all other detailed fields
        Schema::table('yudisium_sidings', function (Blueprint $table) {
            // Dosen Wali
            if (!Schema::hasColumn('yudisium_sidings', 'dosen_wali_nama')) {
                $table->string('dosen_wali_nama')->nullable();
            }
            if (!Schema::hasColumn('yudisium_sidings', 'dosen_wali_foto')) {
                $table->string('dosen_wali_foto')->nullable();
            }
            
            // Pembimbing 1
            if (!Schema::hasColumn('yudisium_sidings', 'pembimbing_1_nama')) {
                $table->string('pembimbing_1_nama')->nullable();
            }
            if (!Schema::hasColumn('yudisium_sidings', 'pembimbing_1_foto')) {
                $table->string('pembimbing_1_foto')->nullable();
            }
            if (!Schema::hasColumn('yudisium_sidings', 'pembimbing_1_nilai')) {
                $table->decimal('pembimbing_1_nilai', 5, 2)->nullable();
            }
            
            // Pembimbing 2
            if (!Schema::hasColumn('yudisium_sidings', 'pembimbing_2_nama')) {
                $table->string('pembimbing_2_nama')->nullable();
            }
            if (!Schema::hasColumn('yudisium_sidings', 'pembimbing_2_nilai')) {
                $table->decimal('pembimbing_2_nilai', 5, 2)->nullable();
            }
            
            // Penguji Ketua
            if (!Schema::hasColumn('yudisium_sidings', 'penguji_ketua_nama')) {
                $table->string('penguji_ketua_nama')->nullable();
            }
            if (!Schema::hasColumn('yudisium_sidings', 'penguji_ketua_nilai')) {
                $table->decimal('penguji_ketua_nilai', 5, 2)->nullable();
            }
            
            // Penguji Anggota
            if (!Schema::hasColumn('yudisium_sidings', 'penguji_anggota_nama')) {
                $table->string('penguji_anggota_nama')->nullable();
            }
            if (!Schema::hasColumn('yudisium_sidings', 'penguji_anggota_nilai')) {
                $table->decimal('penguji_anggota_nilai', 5, 2)->nullable();
            }
            
            // Tugas Akhir
            if (!Schema::hasColumn('yudisium_sidings', 'judul_tugas_akhir')) {
                $table->text('judul_tugas_akhir')->nullable();
            }
            if (!Schema::hasColumn('yudisium_sidings', 'jenis_tugas_akhir')) {
                $table->string('jenis_tugas_akhir')->nullable();
            }
            
            // Nilai Total
            if (!Schema::hasColumn('yudisium_sidings', 'nilai_total')) {
                $table->decimal('nilai_total', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('yudisium_sidings', 'nilai_huruf')) {
                $table->string('nilai_huruf')->nullable(); // AB, A, B, etc
            }
            
            // Predikat dan Status
            if (!Schema::hasColumn('yudisium_sidings', 'predikat_yudisium')) {
                $table->string('predikat_yudisium')->nullable(); // SANGAT MEMUASKAN, etc
            }
            if (!Schema::hasColumn('yudisium_sidings', 'status_cumlaude')) {
                $table->enum('status_cumlaude', ['cumlaude', 'summa_cumlaude', 'tidak'])->nullable();
            }
            if (!Schema::hasColumn('yudisium_sidings', 'pemenuhan_jurnal')) {
                $table->text('pemenuhan_jurnal')->nullable();
            }
            if (!Schema::hasColumn('yudisium_sidings', 'status_yudisium')) {
                $table->enum('status_yudisium', ['lulus', 'tidak_lulus', 'pending'])->default('pending');
            }
            
            // Catatan
            if (!Schema::hasColumn('yudisium_sidings', 'catatan')) {
                $table->text('catatan')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('yudisium_sidings', function (Blueprint $table) {
            // Drop foreign key first if exists
            if (Schema::hasColumn('yudisium_sidings', 'student_id')) {
                try {
                    $table->dropForeign(['student_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
            }
            
            $table->dropColumn([
                'dosen_wali_nama',
                'dosen_wali_foto',
                'pembimbing_1_nama',
                'pembimbing_1_foto',
                'pembimbing_1_nilai',
                'pembimbing_2_nama',
                'pembimbing_2_nilai',
                'penguji_ketua_nama',
                'penguji_ketua_nilai',
                'penguji_anggota_nama',
                'penguji_anggota_nilai',
                'judul_tugas_akhir',
                'jenis_tugas_akhir',
                'nilai_total',
                'nilai_huruf',
                'predikat_yudisium',
                'status_cumlaude',
                'pemenuhan_jurnal',
                'status_yudisium',
                'catatan',
            ]);
        });
    }
};
