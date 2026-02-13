<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID wali kelas berdasarkan email
        $waliKelasX = \DB::table('users')->where('email', 'walikelas.x@spk.com')->first();
        $waliKelasXI = \DB::table('users')->where('email', 'walikelas.xi@spk.com')->first();
        $waliKelasXII = \DB::table('users')->where('email', 'walikelas.xii@spk.com')->first();

        $kelas = [
            [
                'id_kelas' => 'X',
                'nama_kelas' => 'Kelas X',
                'kapasitas' => 25,
                'id_wali_kelas' => $waliKelasX ? $waliKelasX->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'XI',
                'nama_kelas' => 'Kelas XI',
                'kapasitas' => 25,
                'id_wali_kelas' => $waliKelasXI ? $waliKelasXI->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'XII',
                'nama_kelas' => 'Kelas XII',
                'kapasitas' => 25,
                'id_wali_kelas' => $waliKelasXII ? $waliKelasXII->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        \DB::table('tb_kelas')->insert($kelas);
    }
}
