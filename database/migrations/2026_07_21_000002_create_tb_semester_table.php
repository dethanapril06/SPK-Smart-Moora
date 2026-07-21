<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_semester', function (Blueprint $table) {
            $table->id('id_semester');
            $table->unsignedBigInteger('id_ta');
            $table->foreign('id_ta')->references('id_ta')->on('tb_tahun_ajaran')->cascadeOnDelete();
            $table->enum('nama_semester', ['Ganjil', 'Genap']);
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->unique(['id_ta', 'nama_semester'], 'semester_unique_per_ta');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_semester');
    }
};