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
        Schema::table('yudisium_sidings', function (Blueprint $table) {
            // Add student_id column if it doesn't exist
            if (!Schema::hasColumn('yudisium_sidings', 'student_id')) {
                // Check if mahasiswa_id exists
                if (Schema::hasColumn('yudisium_sidings', 'mahasiswa_id')) {
                    // Add student_id as nullable first
                    $table->unsignedBigInteger('student_id')->nullable()->after('periode_id');
                    
                    // Add foreign key constraint
                    $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
                    
                    // Copy data from mahasiswa_id to student_id
                    DB::statement('UPDATE yudisium_sidings SET student_id = mahasiswa_id WHERE student_id IS NULL');
                    
                    // Make student_id not nullable after data migration
                    $table->unsignedBigInteger('student_id')->nullable(false)->change();
                } else {
                    // If mahasiswa_id doesn't exist, add student_id directly
                    $table->foreignId('student_id')->after('periode_id')->constrained('students')->onDelete('cascade');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('yudisium_sidings', function (Blueprint $table) {
            if (Schema::hasColumn('yudisium_sidings', 'student_id')) {
                $table->dropForeign(['student_id']);
                $table->dropColumn('student_id');
            }
        });
    }
};
