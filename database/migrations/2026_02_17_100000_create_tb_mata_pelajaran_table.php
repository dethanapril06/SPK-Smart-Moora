<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_mata_pelajaran', function (Blueprint $table) {
            $table->id('id_mapel');
            $table->string('kode_mapel', 10)->unique();
            $table->string('nama_mapel', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_mata_pelajaran');
    }
};
