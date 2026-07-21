<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $years = DB::table('tb_tahun_ajaran')
                ->select('tahun_ajaran')
                ->distinct()
                ->orderBy('tahun_ajaran')
                ->pluck('tahun_ajaran');

            foreach ($years as $tahunAjaran) {
                $representative = DB::table('tb_tahun_ajaran')
                    ->where('tahun_ajaran', $tahunAjaran)
                    ->orderBy('id_ta')
                    ->first();

                if (!$representative) {
                    continue;
                }

                $activeLegacy = DB::table('tb_tahun_ajaran')
                    ->where('tahun_ajaran', $tahunAjaran)
                    ->where('is_active', 1)
                    ->orderByDesc('id_ta')
                    ->first();

                $activeSemester = $activeLegacy->semester ?? 'Ganjil';

                foreach (['Ganjil', 'Genap'] as $semesterName) {
                    DB::table('tb_semester')->updateOrInsert(
                        [
                            'id_ta' => $representative->id_ta,
                            'nama_semester' => $semesterName,
                        ],
                        [
                            'is_active' => $activeLegacy && $activeSemester === $semesterName,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        });
    }

    public function down(): void
    {
        DB::table('tb_semester')->delete();
    }
};