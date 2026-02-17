<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NilaiPengetahuan;
use App\Models\Siswa;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;

class NilaiPengetahuanSeeder extends Seeder
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
                NilaiPengetahuan::firstOrCreate(
                    [
                        'id_siswa' => $siswa->id_siswa,
                        'id_mapel' => $mapel->id_mapel,
                        'id_ta' => $tahunAjaranAktif->id_ta,
                    ],
                    [
                        'nilai' => rand(65, 100),
                    ]
                );
            }
        }

        $this->command->info('Nilai Pengetahuan seeded: ' . $siswaList->count() . ' siswa × ' . $mapelList->count() . ' mapel.');
    }
}
