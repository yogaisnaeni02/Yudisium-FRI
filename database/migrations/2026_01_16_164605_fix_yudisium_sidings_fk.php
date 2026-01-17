<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixYudisiumSidingsFk extends Migration
{
    public function up(): void
    {
        Schema::table('yudisium_sidings', function (Blueprint $table) {
            if (Schema::hasColumn('yudisium_sidings', 'mahasiswa_id')) {
                try {
                    $table->dropForeign(['mahasiswa_id']);
                } catch (\Exception $e) {}
            }
        });

        DB::statement('
            UPDATE yudisium_sidings
            SET student_id = mahasiswa_id
            WHERE student_id IS NULL
        ');

        Schema::table('yudisium_sidings', function (Blueprint $table) {
            $table->unsignedBigInteger('student_id')->nullable(false)->change();

            if (Schema::hasColumn('yudisium_sidings', 'mahasiswa_id')) {
                $table->dropColumn('mahasiswa_id');
            }
        });
    }

    public function down(): void
    {
        //
    }
}
