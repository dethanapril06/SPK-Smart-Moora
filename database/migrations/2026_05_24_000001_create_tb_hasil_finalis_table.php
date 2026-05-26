<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_hasil_finalis', function (Blueprint $table) {
            $table->id('id_hasil_finalis');
            $table->unsignedBigInteger('id_siswa');
            $table->unsignedBigInteger('id_ta');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('metode', 20);
            $table->float('skor');
            $table->integer('rank');
            $table->integer('source_rank')->default(1);
            $table->timestamps();

            $table->foreign('id_siswa')->references('id_siswa')->on('tb_siswa')->cascadeOnDelete();
            $table->foreign('id_ta')->references('id_ta')->on('tb_tahun_ajaran')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->unique(['id_siswa', 'id_ta', 'user_id', 'metode'], 'hasil_finalis_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_hasil_finalis');
    }
};
