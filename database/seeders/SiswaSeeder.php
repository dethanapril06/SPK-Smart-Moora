<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil tahun ajaran yang aktif
        $tahunAjaranAktif = \DB::table('tb_tahun_ajaran')
            ->where('is_active', true)
            ->first();

        if (!$tahunAjaranAktif) {
            $this->command->error('Tidak ada tahun ajaran yang aktif!');
            return;
        }

        // Daftar kelas
        $kelasIds = ['X.1', 'X.2', 'X.3', 'X.4', 'X.5', 'X.6', 'X.7', 'X.8', 'X.9', 'X.10', 'X.11', 'X.12', 'XI.Alam.1', 'XI.Alam.2', 'XI.Alam.3', 'XI.Alam.4', 'XI.Sosial.1', 'XI.Sosial.2', 'XI.Sosial.3', 'XI.Sosial.4', 'XI.Sosial.5', 'XI.Sosial.6', 'XI.Sosial.7', 'XI.Bahasa.1', 'XII.Alam.1', 'XII.Alam.2', 'XII.Alam.3', 'XII.Alam.4', 'XII.Sosial.1', 'XII.Sosial.2', 'XII.Sosial.3', 'XII.Sosial.4', 'XII.Sosial.5', 'XII.Sosial.6', 'XII.Sosial.7', 'XII.Bahasa.1'];

        // Generate 20 siswa untuk setiap kelas
        foreach ($kelasIds as $kelasId) {
            \App\Models\Siswa::factory()
                ->count(36)
                ->create([
                    'id_kelas' => $kelasId,
                    'id_ta' => $tahunAjaranAktif->id_ta,
                ]);

            $this->command->info("Created 36 students for Kelas {$kelasId}");
        }

        $this->command->info('Total: 648 students created successfully!');
    }
}
