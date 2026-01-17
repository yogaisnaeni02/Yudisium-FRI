<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYudisiumSidingsTable extends Migration
{
    public function up()
    {
        Schema::create('yudisium_sidings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained()->onDelete('cascade');
            $table->foreignId('mahasiswa_id')->constrained('students')->onDelete('cascade');
            $table->date('tanggal_sidang');
            $table->string('predikat');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('yudisium_sidings');
    }
}