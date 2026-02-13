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
        $kelasIds = ['X', 'XI', 'XII'];

        // Generate 20 siswa untuk setiap kelas
        foreach ($kelasIds as $kelasId) {
            \App\Models\Siswa::factory()
                ->count(20)
                ->create([
                    'id_kelas' => $kelasId,
                    'id_ta' => $tahunAjaranAktif->id_ta,
                ]);

            $this->command->info("Created 20 students for Kelas {$kelasId}");
        }

        $this->command->info('Total: 60 students created successfully!');
    }
}
