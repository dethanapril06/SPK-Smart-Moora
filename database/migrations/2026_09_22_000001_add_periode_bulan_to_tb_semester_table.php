<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_semester', function (Blueprint $table) {
            $table->string('periode_bulan', 50)->nullable()->after('nama_semester');
        });

        // Set default periode bulan for existing semesters
        DB::table('tb_semester')->where('nama_semester', 'Ganjil')->update(['periode_bulan' => 'Juli - Desember']);
        DB::table('tb_semester')->where('nama_semester', 'Genap')->update(['periode_bulan' => 'Januari - Juni']);
    }

    public function down(): void
    {
        Schema::table('tb_semester', function (Blueprint $table) {
            $table->dropColumn('periode_bulan');
        });
    }
};
