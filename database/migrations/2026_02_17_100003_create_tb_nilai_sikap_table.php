<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_nilai_sikap', function (Blueprint $table) {
            $table->id('id_nilai_sikap');
            $table->unsignedBigInteger('id_siswa');
            $table->foreign('id_siswa')->references('id_siswa')->on('tb_siswa')->cascadeOnDelete();
            $table->unsignedBigInteger('id_ta');
            $table->foreign('id_ta')->references('id_ta')->on('tb_tahun_ajaran')->cascadeOnDelete();
            $table->enum('sikap_spiritual', ['Sangat Baik', 'Baik', 'Cukup', 'Kurang']);
            $table->enum('sikap_sosial', ['Sangat Baik', 'Baik', 'Cukup', 'Kurang']);
            $table->timestamps();

            $table->unique(['id_siswa', 'id_ta'], 'nilai_sikap_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_nilai_sikap');
    }
};
