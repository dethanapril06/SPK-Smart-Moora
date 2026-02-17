<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_nilai_ekstrakurikuler', function (Blueprint $table) {
            $table->id('id_nilai_ekskul');
            $table->unsignedBigInteger('id_siswa');
            $table->foreign('id_siswa')->references('id_siswa')->on('tb_siswa')->cascadeOnDelete();
            $table->unsignedBigInteger('id_ta');
            $table->foreign('id_ta')->references('id_ta')->on('tb_tahun_ajaran')->cascadeOnDelete();
            $table->string('nama_ekskul', 50);
            $table->enum('predikat', ['Sangat Baik', 'Baik', 'Cukup', 'Kurang']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_nilai_ekstrakurikuler');
    }
};
