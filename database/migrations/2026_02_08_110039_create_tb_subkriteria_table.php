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
        Schema::create('tb_subkriteria', function (Blueprint $table) {
            $table->id('id_subkriteria');
            $table->unsignedBigInteger('id_kriteria');
            $table->foreign('id_kriteria')->references('id_kriteria')->on('tb_kriteria')->cascadeOnDelete();
            $table->string('nama_subkriteria', 25)->nullable();
            $table->double('nilai_awal', 15, 2)->nullable();
            $table->double('nilai_akhir', 15, 2)->nullable();
            $table->float('bobot_subkriteria')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_subkriteria');
    }
};
