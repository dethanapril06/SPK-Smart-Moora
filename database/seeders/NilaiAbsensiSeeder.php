<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NilaiAbsensi;
use App\Models\Siswa;
use App\Models\TahunAjaran;

class NilaiAbsensiSeeder extends Seeder
{
    public function run(): void
    {
        $siswaList = Siswa::all();
        $tahunAjaranAktif = TahunAjaran::where('is_active', 1)->first();

        if (!$tahunAjaranAktif) {
            $this->command->warn('Tidak ada tahun ajaran aktif.');
            return;
        }

        foreach ($siswaList as $siswa) {
            NilaiAbsensi::firstOrCreate(
                [
                    'id_siswa' => $siswa->id_siswa,
                    'id_ta' => $tahunAjaranAktif->id_ta,
                ],
                [
                    'jumlah_sakit' => rand(0, 5),
                    'jumlah_izin' => rand(0, 3),
                    'jumlah_alpa' => rand(0, 4),
                ]
            );
        }

        $this->command->info('Nilai Absensi seeded: ' . $siswaList->count() . ' siswa.');
    }
}
