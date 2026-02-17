<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_nilai_absensi', function (Blueprint $table) {
            $table->id('id_nilai_absensi');
            $table->unsignedBigInteger('id_siswa');
            $table->foreign('id_siswa')->references('id_siswa')->on('tb_siswa')->cascadeOnDelete();
            $table->unsignedBigInteger('id_ta');
            $table->foreign('id_ta')->references('id_ta')->on('tb_tahun_ajaran')->cascadeOnDelete();
            $table->integer('jumlah_sakit')->default(0);
            $table->integer('jumlah_izin')->default(0);
            $table->integer('jumlah_alpa')->default(0);
            $table->timestamps();

            $table->unique(['id_siswa', 'id_ta'], 'nilai_absensi_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_nilai_absensi');
    }
};
