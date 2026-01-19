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
        Schema::table('yudisium_sidings', function (Blueprint $table) {
            $table->datetime('tanggal_sidang')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('yudisium_sidings', function (Blueprint $table) {
            $table->date('tanggal_sidang')->change();
        });
    }
};
