<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NilaiEkstrakurikuler;
use App\Models\Siswa;
use App\Models\TahunAjaran;

class NilaiEkstrakurikulerSeeder extends Seeder
{
    public function run(): void
    {
        $siswaList = Siswa::all();
        $tahunAjaranAktif = TahunAjaran::where('is_active', 1)->first();

        $ekskulOptions = [
            'Pramuka', 'PMR', 'Paskibra', 'Futsal', 'Basket',
            'Voli', 'Badminton', 'Seni Tari', 'Paduan Suara', 'Robotik',
            'English Club', 'KIR', 'Pencak Silat', 'Karate', 'Drum Band',
        ];
        $predikatList = ['Sangat Baik', 'Baik', 'Cukup', 'Kurang'];
        $weightedPredikat = ['Sangat Baik', 'Baik', 'Baik', 'Baik', 'Cukup'];

        if (!$tahunAjaranAktif) {
            $this->command->warn('Tidak ada tahun ajaran aktif.');
            return;
        }

        foreach ($siswaList as $siswa) {
            // Setiap siswa ikut 1-3 ekskul
            $jumlahEkskul = rand(1, 3);
            $selectedEkskul = array_rand(array_flip($ekskulOptions), $jumlahEkskul);
            if (!is_array($selectedEkskul)) {
                $selectedEkskul = [$selectedEkskul];
            }

            foreach ($selectedEkskul as $ekskul) {
                NilaiEkstrakurikuler::firstOrCreate(
                    [
                        'id_siswa' => $siswa->id_siswa,
                        'id_ta' => $tahunAjaranAktif->id_ta,
                        'nama_ekskul' => $ekskul,
                    ],
                    [
                        'predikat' => $weightedPredikat[array_rand($weightedPredikat)],
                    ]
                );
            }
        }

        $this->command->info('Nilai Ekstrakurikuler seeded: ' . $siswaList->count() . ' siswa (1-3 ekskul masing-masing).');
    }
}
