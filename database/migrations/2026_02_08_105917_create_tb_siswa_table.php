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
        Schema::create('tb_siswa', function (Blueprint $table) {
            $table->id('id_siswa');
            $table->string('nisn', 20)->unique();
            $table->string('nama_siswa', 50);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('alamat', 50)->nullable();
            $table->string('id_kelas', 10)->nullable();
            $table->foreign('id_kelas')->references('id_kelas')->on('tb_kelas')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_siswa');
    }
};
