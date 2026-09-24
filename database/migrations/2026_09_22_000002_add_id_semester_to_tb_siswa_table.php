<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_siswa', function (Blueprint $table) {
            $table->unsignedBigInteger('id_semester')->nullable()->after('id_ta');
            $table->foreign('id_semester')->references('id_semester')->on('tb_semester')->nullOnDelete();
        });

        // Backfill existing siswa to their TA's 'Ganjil' semester by default
        $semesters = DB::table('tb_semester')
            ->where('nama_semester', 'Ganjil')
            ->get();

        foreach ($semesters as $semester) {
            DB::table('tb_siswa')
                ->where('id_ta', $semester->id_ta)
                ->whereNull('id_semester')
                ->update(['id_semester' => $semester->id_semester]);
        }
    }

    public function down(): void
    {
        Schema::table('tb_siswa', function (Blueprint $table) {
            $table->dropForeign(['id_semester']);
            $table->dropColumn('id_semester');
        });
    }
};
