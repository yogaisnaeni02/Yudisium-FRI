<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('nama');
            $table->string('prodi')->nullable()->after('foto');
            $table->integer('tak')->nullable()->after('prodi');
            $table->string('pembimbing_1')->nullable()->after('tak');
            $table->string('pembimbing_2')->nullable()->after('pembimbing_1');
            $table->string('penguji_ketua')->nullable()->after('pembimbing_2');
            $table->string('penguji_anggota')->nullable()->after('penguji_ketua');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'foto',
                'prodi',
                'tak',
                'pembimbing_1',
                'pembimbing_2',
                'penguji_ketua',
                'penguji_anggota',
            ]);
        });
    }
};
