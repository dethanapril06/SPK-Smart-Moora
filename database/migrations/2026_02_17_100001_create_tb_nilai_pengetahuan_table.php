<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_nilai_pengetahuan', function (Blueprint $table) {
            $table->id('id_nilai_pengetahuan');
            $table->unsignedBigInteger('id_siswa');
            $table->foreign('id_siswa')->references('id_siswa')->on('tb_siswa')->cascadeOnDelete();
            $table->unsignedBigInteger('id_mapel');
            $table->foreign('id_mapel')->references('id_mapel')->on('tb_mata_pelajaran')->cascadeOnDelete();
            $table->unsignedBigInteger('id_ta');
            $table->foreign('id_ta')->references('id_ta')->on('tb_tahun_ajaran')->cascadeOnDelete();
            $table->float('nilai')->nullable();
            $table->timestamps();

            $table->unique(['id_siswa', 'id_mapel', 'id_ta'], 'nilai_pengetahuan_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_nilai_pengetahuan');
    }
};
