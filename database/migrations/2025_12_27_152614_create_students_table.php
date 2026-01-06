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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nim')->unique();
            $table->string('nama');
            $table->decimal('ipk', 3, 2);
            $table->integer('total_sks');
            $table->enum('status_kelulusan', ['lulus', 'belum_lulus'])->default('belum_lulus');
            $table->json('mata_kuliah')->nullable(); // Status kelulusan mata kuliah
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
