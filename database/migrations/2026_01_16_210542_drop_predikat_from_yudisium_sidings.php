<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('yudisium_sidings', function (Blueprint $table) {
            if (Schema::hasColumn('yudisium_sidings', 'predikat')) {
                $table->dropColumn('predikat');
            }
        });
    }

    public function down(): void
    {
        Schema::table('yudisium_sidings', function (Blueprint $table) {
            $table->string('predikat')->nullable();
        });
    }
};
