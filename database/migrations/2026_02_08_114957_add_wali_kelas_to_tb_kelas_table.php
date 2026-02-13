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
            $table->unsignedBigInteger('id_wali_kelas')->nullable()->after('nama_kelas');
            $table->foreign('id_wali_kelas')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_kelas', function (Blueprint $table) {
            $table->dropForeign(['id_wali_kelas']);
            $table->dropColumn('id_wali_kelas');
        });
    }
};
