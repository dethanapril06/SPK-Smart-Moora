<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NilaiKeterampilan;
use App\Models\Siswa;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;

class NilaiKeterampilanSeeder extends Seeder
{
    public function run(): void
    {
        $siswaList = Siswa::all();
        $mapelList = MataPelajaran::all();
        $tahunAjaranAktif = TahunAjaran::where('is_active', 1)->first();

        if (!$tahunAjaranAktif || $mapelList->isEmpty()) {
            $this->command->warn('Data tahun ajaran aktif atau mata pelajaran belum tersedia.');
            return;
        }

        foreach ($siswaList as $siswa) {
            foreach ($mapelList as $mapel) {
                NilaiKeterampilan::firstOrCreate(
                    [
                        'id_siswa' => $siswa->id_siswa,
                        'id_mapel' => $mapel->id_mapel,
                        'id_ta' => $tahunAjaranAktif->id_ta,
                    ],
                    [
                        'nilai' => rand(60, 95),
                    ]
                );
            }
        }

        $this->command->info('Nilai Keterampilan seeded: ' . $siswaList->count() . ' siswa × ' . $mapelList->count() . ' mapel.');
    }
}
