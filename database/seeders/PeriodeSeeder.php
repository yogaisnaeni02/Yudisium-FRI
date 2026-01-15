<?php

namespace Database\Seeders;

use App\Models\Periode;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PeriodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Periode::create([
            'nama' => 'Periode Yudisium 2026 - Genap',
            'tanggal_mulai' => '2026-02-01',
            'tanggal_selesai' => '2026-04-30',
            'status' => 'active',
        ]);

        Periode::create([
            'nama' => 'Periode Yudisium 2026 - Ganjil',
            'tanggal_mulai' => '2026-08-01',
            'tanggal_selesai' => '2026-10-31',
            'status' => 'inactive',
        ]);

        Periode::create([
            'nama' => 'Periode Yudisium 2025 - Genap',
            'tanggal_mulai' => '2025-02-01',
            'tanggal_selesai' => '2025-04-30',
            'status' => 'inactive',
        ]);
    }
}
