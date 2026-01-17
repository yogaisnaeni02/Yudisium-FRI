<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('yudisium_sidings', function (Blueprint $table) {

            // === PEMBIMBING 2 ===
            $table->string('pembimbing_2_foto')->nullable();

            // === PENGUJI KETUA ===
            $table->string('penguji_ketua_foto')->nullable();

            // === PENGUJI ANGGOTA ===
            $table->string('penguji_anggota_foto')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('yudisium_sidings', function (Blueprint $table) {
            $table->dropColumn([
                'pembimbing_2_foto',
                'penguji_ketua_foto',
                'penguji_anggota_foto'
            ]);
        });
    }
};
