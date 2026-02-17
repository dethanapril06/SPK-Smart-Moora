<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NilaiSikap;
use App\Models\Siswa;
use App\Models\TahunAjaran;

class NilaiSikapSeeder extends Seeder
{
    public function run(): void
    {
        $siswaList = Siswa::all();
        $tahunAjaranAktif = TahunAjaran::where('is_active', 1)->first();
        $predikatList = ['Sangat Baik', 'Baik', 'Cukup', 'Kurang'];
        // Weighted: lebih banyak Baik dan Sangat Baik
        $weightedPredikat = ['Sangat Baik', 'Sangat Baik', 'Baik', 'Baik', 'Baik', 'Cukup'];

        if (!$tahunAjaranAktif) {
            $this->command->warn('Tidak ada tahun ajaran aktif.');
            return;
        }

        foreach ($siswaList as $siswa) {
            NilaiSikap::firstOrCreate(
                [
                    'id_siswa' => $siswa->id_siswa,
                    'id_ta' => $tahunAjaranAktif->id_ta,
                ],
                [
                    'sikap_spiritual' => $weightedPredikat[array_rand($weightedPredikat)],
                    'sikap_sosial' => $weightedPredikat[array_rand($weightedPredikat)],
                ]
            );
        }

        $this->command->info('Nilai Sikap seeded: ' . $siswaList->count() . ' siswa.');
    }
}
