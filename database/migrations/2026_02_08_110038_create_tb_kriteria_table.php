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
        Schema::create('tb_kriteria', function (Blueprint $table) {
            $table->id('id_kriteria');
            $table->string('kode_kriteria', 5)->unique();
            $table->string('nama_kriteria', 50);
            $table->enum('jenis_kriteria', ['Benefit', 'Cost']);
            $table->float('bobot');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_kriteria');
    }
};
