<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $kelasIds = DB::table('tb_kelas')->pluck('id_kelas');
        $mapelIds = DB::table('tb_mata_pelajaran')->pluck('id_mapel');

        foreach ($kelasIds as $idKelas) {
            $rows = $mapelIds->map(fn ($idMapel) => [
                'id_kelas' => $idKelas,
                'id_mapel' => $idMapel,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();

            if (!empty($rows)) {
                DB::table('tb_kelas_mata_pelajaran')->insertOrIgnore($rows);
            }
        }
    }

    public function down(): void
    {
        //
    }
};
