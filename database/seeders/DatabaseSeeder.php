<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\Submission;
use App\Models\Document;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::firstOrCreate(
            ['email' => 'admin@yudisium.com'],
            [
            'name' => 'Admin User',
            'password' => bcrypt('password'),
            'role' => 'admin',
            ]
        );

        // Create Student User 1
        $studentUser = User::firstOrCreate(
            ['email' => 'student@yudisium.com'],
            [
            'name' => 'Student User',
            'password' => bcrypt('password'),
            'role' => 'student',
            ]
        );

        $student1 = Student::firstOrCreate(
            ['nim' => '123456789'],
            [
            'user_id' => $studentUser->id,
            'nama' => 'Student User',
            'prodi' => 'S1 Sistem Informasi',
            'tak' => 85,
            'ipk' => 3.75,
            'total_sks' => 144,
            'skor_eprt' => 560,
            'status_kelulusan' => 'belum_lulus',
            ]
        );

        // Create submission for student 1
        $submission1 = Submission::firstOrCreate(
            ['student_id' => $student1->id],
            [
            'status' => 'under_review',
            'submitted_at' => now()->subDays(3),
            ]
        );

        // Create documents for submission 1
        $documentTypes = [
            'Surat Pernyataan',
            'Form Biodata Izajah & Transkip',
            'KTP',
            'Akta Lahir',
            'Ijazah Pendidikan Terakhir',
            'Buku TA yang Disahkan',
            'Slide PPT',
            'Screenshot (Gracias)',
            'Berkas Referensi (Minimal 10)',
            'Bukti Approval Revisi (SOFI)',
            'Bukti Approval SKPI',
            'Surat Keterangan Bebas Pustaka (SKBP)',
            'Dokumen Cumlaude (Publikasi/Lomba/HKI)',
            'Dokumen Pendukung Tambahan',
        ];

        $statuses = ['approved', 'approved', 'revision', 'pending', 'approved', 'pending', 'approved', 'rejected', 'pending', 'pending', 'pending', 'pending', 'pending', 'pending'];

        foreach ($documentTypes as $key => $docType) {
            Document::firstOrCreate(
                [
                'submission_id' => $submission1->id,
                'type' => $docType,
                ],
                [
                'name' => str_replace('/', '_', $docType) . '.pdf',
                'file_path' => 'yudisium/documents/' . str_replace('/', '_', $docType) . '.pdf',
                'status' => $statuses[$key],
                'feedback' => $statuses[$key] === 'revision' ? 'Silakan upload ulang dengan kualitas lebih baik' : 
                              ($statuses[$key] === 'rejected' ? 'Dokumen tidak sesuai dengan format yang diharapkan' : null),
                ]
            );
        }

        // Create Student User 2
        $studentUser2 = User::firstOrCreate(
            ['email' => 'student2@yudisium.com'],
            [
            'name' => 'Student User 2',
            'password' => bcrypt('password'),
            'role' => 'student',
            ]
        );

        $student2 = Student::firstOrCreate(
            ['nim' => '987654321'],
            [
            'user_id' => $studentUser2->id,
            'nama' => 'Student User 2',
            'prodi' => 'S1 Sistem Informasi',
            'tak' => 78,
            'ipk' => 3.50,
            'total_sks' => 144,
            'skor_eprt' => 510,
            'status_kelulusan' => 'belum_lulus',
            ]
        );

        // Create submission for student 2
        $submission2 = Submission::firstOrCreate(
            ['student_id' => $student2->id],
            [
            'status' => 'draft',
            ]
        );

        // Seed articles
        $this->call(ArticleSeeder::class);
    }
}
