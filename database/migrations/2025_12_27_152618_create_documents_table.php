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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->onDelete('cascade');
            $table->string('type'); // e.g., 'wajib', 'pendukung'
            $table->string('name');
            $table->string('file_path');
            $table->enum('status', ['pending', 'approved', 'revision', 'rejected'])->default('pending');
            $table->text('feedback')->nullable();
            $table->json('metadata')->nullable(); // OCR data, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
