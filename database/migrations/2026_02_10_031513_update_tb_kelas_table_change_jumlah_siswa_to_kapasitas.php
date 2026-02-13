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
        Schema::table('tb_kelas', function (Blueprint $table) {
            // Rename jumlah_siswa to kapasitas
            $table->renameColumn('jumlah_siswa', 'kapasitas');
            
            // Add id_wali_kelas if not exists
            if (!Schema::hasColumn('tb_kelas', 'id_wali_kelas')) {
                $table->unsignedBigInteger('id_wali_kelas')->nullable()->after('nama_kelas');
                $table->foreign('id_wali_kelas')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_kelas', function (Blueprint $table) {
            // Rename back kapasitas to jumlah_siswa
            $table->renameColumn('kapasitas', 'jumlah_siswa');
            
            // Drop id_wali_kelas if exists
            if (Schema::hasColumn('tb_kelas', 'id_wali_kelas')) {
                $table->dropForeign(['id_wali_kelas']);
                $table->dropColumn('id_wali_kelas');
            }
        });
    }
};
