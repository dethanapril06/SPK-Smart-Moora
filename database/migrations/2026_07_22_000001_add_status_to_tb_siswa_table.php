<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_siswa', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'lulus'])->default('aktif')->after('id_ta');
            $table->year('tahun_lulus')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('tb_siswa', function (Blueprint $table) {
            $table->dropColumn(['status', 'tahun_lulus']);
        });
    }
};
