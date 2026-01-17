<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user (first admin or create one)
        $admin = User::firstOrCreate(
            ['email' => 'admin@yudisium.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        $articles = [
            [
                'title' => 'Yudisium Gelombang I Tahun 2026',
                'content' => 'Yudisium Fakultas Rekayasa Industri Gelombang I akan dilaksanakan pada tanggal 3 - 7 Juli 2026. Bagi mahasiswa yang telah menyelesaikan semua persyaratan, silakan melakukan pengajuan yudisium melalui sistem ini.

Penting:
- Pastikan semua dokumen telah lengkap dan terverifikasi
- Pengajuan harus dilakukan sebelum tanggal 30 Juni 2026
- Untuk informasi lebih lanjut, hubungi bagian akademik Fakultas Rekayasa Industri',
                'excerpt' => 'Yudisium Gelombang I akan dilaksanakan pada tanggal 3 - 7 Juli 2026',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(5),
            ],
            [
                'title' => 'Yudisium Gelombang II Tahun 2026',
                'content' => 'Yudisium Fakultas Rekayasa Industri Gelombang II akan dilaksanakan pada tanggal 15 - 19 Agustus 2026. Bagi mahasiswa yang belum mengikuti Gelombang I atau yang pengajuannya sedang dalam proses, dapat mengikuti Gelombang II.

Informasi Penting:
- Batas akhir pengajuan: 5 Agustus 2026
- Pastikan semua dokumen sudah lengkap
- Verifikasi dokumen akan dilakukan maksimal 7 hari sebelum tanggal yudisium',
                'excerpt' => 'Yudisium Gelombang II akan dilaksanakan pada tanggal 15 - 19 Agustus 2026',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(3),
            ],
            [
                'title' => 'Panduan Upload Dokumen Yudisium',
                'content' => 'Berikut adalah panduan lengkap untuk mengunggah dokumen yudisium:

1. Pastikan file dalam format PDF, DOC, atau DOCX
2. Ukuran maksimal file: 5MB per dokumen
3. Untuk dokumen yang memerlukan tanda tangan, pastikan sudah ditandatangani
4. Scan dokumen dengan kualitas minimal 300 DPI
5. Pastikan semua dokumen dapat terbaca dengan jelas

Jika mengalami kendala, silakan hubungi admin melalui sistem atau datang langsung ke bagian akademik.',
                'excerpt' => 'Panduan lengkap untuk mengunggah dokumen yudisium dengan benar',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(7),
            ],
            [
                'title' => 'Informasi Jadwal Verifikasi Dokumen',
                'content' => 'Proses verifikasi dokumen yudisium dilakukan oleh admin pada hari kerja (Senin - Jumat) pukul 08:00 - 16:00 WIB.

Timeline Verifikasi:
- Dokumen yang diupload sebelum pukul 12:00 WIB akan diverifikasi pada hari yang sama
- Dokumen yang diupload setelah pukul 12:00 WIB akan diverifikasi pada hari kerja berikutnya
- Notifikasi status dokumen akan dikirim melalui email dan dapat dilihat di dashboard

Pastikan untuk memeriksa status dokumen secara berkala dan segera lakukan revisi jika diperlukan.',
                'excerpt' => 'Informasi jadwal dan proses verifikasi dokumen yudisium',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(2),
            ],
            [
                'title' => 'Pengumuman Penerimaan Pengajuan Yudisium',
                'content' => 'Fakultas Rekayasa Industri membuka pendaftaran pengajuan yudisium untuk semester genap tahun akademik 2025/2026.

Syarat Pengajuan:
1. Telah menyelesaikan semua mata kuliah
2. IPK minimal 2.00
3. Telah menyelesaikan tugas akhir/skripsi
4. Tidak ada tunggakan administrasi
5. Semua dokumen telah lengkap

Bagi mahasiswa yang memenuhi syarat, silakan melakukan pengajuan melalui sistem SIYU.',
                'excerpt' => 'Pengumuman pembukaan pendaftaran pengajuan yudisium semester genap 2025/2026',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(10),
            ],
            [
                'title' => 'Ketentuan Dokumen Cumlaude',
                'content' => 'Bagi mahasiswa dengan IPK ≥ 3.50 yang ingin memperoleh predikat Cumlaude, berikut ketentuannya:

Persyaratan:
1. IPK minimal 3.50
2. Masa studi maksimal 8 semester (untuk S1 reguler) atau 10 semester (untuk S1 transfer)
3. Tidak ada nilai D untuk semua mata kuliah
4. Menyertakan dokumen pendukung (Publikasi, Lomba, atau HKI)

Dokumen pendukung yang dapat disertakan:
- Sertifikat publikasi ilmiah
- Sertifikat kejuaraan/lomba
- Sertifikat Hak Kekayaan Intelektual (HKI)
- Dokumen prestasi akademik lainnya',
                'excerpt' => 'Ketentuan dan persyaratan untuk memperoleh predikat Cumlaude',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(1),
            ],
        ];

        foreach ($articles as $articleData) {
            Article::firstOrCreate(
                ['slug' => Str::slug($articleData['title'])],
                array_merge($articleData, [
                    'slug' => Str::slug($articleData['title']),
                'user_id' => $admin->id,
                ])
            );
        }
    }
}

