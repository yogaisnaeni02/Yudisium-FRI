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
        Schema::create('yudisium_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->decimal('ipk', 3, 2);
            $table->string('predikat_kelulusan');
            $table->enum('status_pembimbing', ['approved', 'pending'])->default('pending');
            $table->enum('status_penguji', ['approved', 'pending'])->default('pending');
            $table->enum('status_kelulusan', ['lulus', 'tidak_lulus'])->default('tidak_lulus');
            $table->boolean('cumlaude')->default(false);
            $table->string('title_cumlaude')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yudisium_results');
    }
};
