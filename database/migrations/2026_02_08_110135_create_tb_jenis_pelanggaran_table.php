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
        Schema::create('tb_jenis_pelanggaran', function (Blueprint $table) {
            $table->id('id_jenis_pelanggaran');
            $table->enum('kategori_pelanggaran', [
                'Keterlambatan', 
                'Kehadiran', 
                'Pakaian', 
                'Kelakuan', 
                'Ketertiban', 
                'Kerajinan', 
                'Narkoba_Miras', 
                'Tata_Tertib_Ujian'
            ]);
            $table->text('nama_pelanggaran');
            $table->integer('bobot_poin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_jenis_pelanggaran');
    }
};
