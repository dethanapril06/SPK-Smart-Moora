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
        $waliKelasX1 = \DB::table('users')->where('email', 'walikelas.x.1@spk.com')->first();
        $waliKelasX2 = \DB::table('users')->where('email', 'walikelas.x.2@spk.com')->first();
        $waliKelasX3 = \DB::table('users')->where('email', 'walikelas.x.3@spk.com')->first();
        $waliKelasX4 = \DB::table('users')->where('email', 'walikelas.x.4@spk.com')->first();
        $waliKelasX5 = \DB::table('users')->where('email', 'walikelas.x.5@spk.com')->first();
        $waliKelasX6 = \DB::table('users')->where('email', 'walikelas.x.6@spk.com')->first();
        $waliKelasX7 = \DB::table('users')->where('email', 'walikelas.x.7@spk.com')->first();
        $waliKelasX8 = \DB::table('users')->where('email', 'walikelas.x.8@spk.com')->first();
        $waliKelasX9 = \DB::table('users')->where('email', 'walikelas.x.9@spk.com')->first();
        $waliKelasX10 = \DB::table('users')->where('email', 'walikelas.x.10@spk.com')->first();
        $waliKelasX11 = \DB::table('users')->where('email', 'walikelas.x.11@spk.com')->first();
        $waliKelasX12 = \DB::table('users')->where('email', 'walikelas.x.12@spk.com')->first();

        $waliKelasXIAlam1 = \DB::table('users')->where('email', 'walikelas.xi.alam.1@spk.com')->first();
        $waliKelasXIAlam2 = \DB::table('users')->where('email', 'walikelas.xi.alam.2@spk.com')->first();
        $waliKelasXIAlam3 = \DB::table('users')->where('email', 'walikelas.xi.alam.3@spk.com')->first();
        $waliKelasXIAlam4 = \DB::table('users')->where('email', 'walikelas.xi.alam.4@spk.com')->first();
        $waliKelasXISosial1 = \DB::table('users')->where('email', 'walikelas.xi.sosial.1@spk.com')->first();
        $waliKelasXISosial2 = \DB::table('users')->where('email', 'walikelas.xi.sosial.2@spk.com')->first();
        $waliKelasXISosial3 = \DB::table('users')->where('email', 'walikelas.xi.sosial.3@spk.com')->first();
        $waliKelasXISosial4 = \DB::table('users')->where('email', 'walikelas.xi.sosial.4@spk.com')->first();
        $waliKelasXISosial5 = \DB::table('users')->where('email', 'walikelas.xi.sosial.5@spk.com')->first();
        $waliKelasXISosial6 = \DB::table('users')->where('email', 'walikelas.xi.sosial.6@spk.com')->first();
        $waliKelasXISosial7 = \DB::table('users')->where('email', 'walikelas.xi.sosial.7@spk.com')->first();
        $waliKelasXIBahasa1 = \DB::table('users')->where('email', 'walikelas.xi.bahasa.1@spk.com')->first();

        $waliKelasXIIAlam1 = \DB::table('users')->where('email', 'walikelas.xii.alam.1@spk.com')->first();
        $waliKelasXIIAlam2 = \DB::table('users')->where('email', 'walikelas.xii.alam.2@spk.com')->first();
        $waliKelasXIIAlam3 = \DB::table('users')->where('email', 'walikelas.xii.alam.3@spk.com')->first();
        $waliKelasXIIAlam4 = \DB::table('users')->where('email', 'walikelas.xii.alam.4@spk.com')->first();
        $waliKelasXIISosial1 = \DB::table('users')->where('email', 'walikelas.xii.sosial.1@spk.com')->first();
        $waliKelasXIISosial2 = \DB::table('users')->where('email', 'walikelas.xii.sosial.2@spk.com')->first();
        $waliKelasXIISosial3 = \DB::table('users')->where('email', 'walikelas.xii.sosial.3@spk.com')->first();
        $waliKelasXIISosial4 = \DB::table('users')->where('email', 'walikelas.xii.sosial.4@spk.com')->first();
        $waliKelasXIISosial5 = \DB::table('users')->where('email', 'walikelas.xii.sosial.5@spk.com')->first();
        $waliKelasXIISosial6 = \DB::table('users')->where('email', 'walikelas.xii.sosial.6@spk.com')->first();
        $waliKelasXIISosial7 = \DB::table('users')->where('email', 'walikelas.xii.sosial.7@spk.com')->first();
        $waliKelasXIIBahasa1 = \DB::table('users')->where('email', 'walikelas.xii.bahasa.1@spk.com')->first();



        $kelas = [
            [
                'id_kelas' => 'X.1',
                'nama_kelas' => 'Kelas X.1',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasX1 ? $waliKelasX1->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'X.2',
                'nama_kelas' => 'Kelas X.2',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasX2 ? $waliKelasX2->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'X.3',
                'nama_kelas' => 'Kelas X.3',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasX3 ? $waliKelasX3->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'X.4',
                'nama_kelas' => 'Kelas X.4',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasX4 ? $waliKelasX4->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'X.5',
                'nama_kelas' => 'Kelas X.5',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasX5 ? $waliKelasX5->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'X.6',
                'nama_kelas' => 'Kelas X.6',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasX6 ? $waliKelasX6->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'X.7',
                'nama_kelas' => 'Kelas X.7',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasX7 ? $waliKelasX7->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'X.8',
                'nama_kelas' => 'Kelas X.8',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasX8 ? $waliKelasX8->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'X.9',
                'nama_kelas' => 'Kelas X.9',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasX9 ? $waliKelasX9->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'X.10',
                'nama_kelas' => 'Kelas X.10',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasX10 ? $waliKelasX10->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'X.11',
                'nama_kelas' => 'Kelas X.11',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasX11 ? $waliKelasX11->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'X.12',
                'nama_kelas' => 'Kelas X.12',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasX12 ? $waliKelasX12->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'XI.Alam.1',
                'nama_kelas' => 'Kelas XI.Alam.1',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasXIAlam1 ? $waliKelasXIAlam1->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'XI.Alam.2',
                'nama_kelas' => 'Kelas XI.Alam.2',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasXIAlam2 ? $waliKelasXIAlam2->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'XI.Alam.3',
                'nama_kelas' => 'Kelas XI.Alam.3',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasXIAlam3 ? $waliKelasXIAlam3->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'XI.Alam.4',
                'nama_kelas' => 'Kelas XI.Alam.4',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasXIAlam4 ? $waliKelasXIAlam4->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'XI.Sosial.1',
                'nama_kelas' => 'Kelas XI.Sosial.1',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasXISosial1 ? $waliKelasXISosial1->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'XI.Sosial.2',
                'nama_kelas' => 'Kelas XI.Sosial.2',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasXISosial2 ? $waliKelasXISosial2->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'XI.Sosial.3',
                'nama_kelas' => 'Kelas XI.Sosial.3',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasXISosial3 ? $waliKelasXISosial3->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'XI.Sosial.4',
                'nama_kelas' => 'Kelas XI.Sosial.4',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasXISosial4 ? $waliKelasXISosial4->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'XI.Sosial.5',
                'nama_kelas' => 'Kelas XI.Sosial.5',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasXISosial5 ? $waliKelasXISosial5->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'XI.Sosial.6',
                'nama_kelas' => 'Kelas XI.Sosial.6',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasXISosial6 ? $waliKelasXISosial6->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'XI.Sosial.7',
                'nama_kelas' => 'Kelas XI.Sosial.7',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasXISosial7 ? $waliKelasXISosial7->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'XI.Bahasa.1',
                'nama_kelas' => 'Kelas XI.Bahasa.1',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasXIBahasa1 ? $waliKelasXIBahasa1->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'XII.Alam.1',
                'nama_kelas' => 'Kelas XII.Alam.1',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasXIIAlam1 ? $waliKelasXIIAlam1->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'XII.Alam.2',
                'nama_kelas' => 'Kelas XII.Alam.2',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasXIIAlam2 ? $waliKelasXIIAlam2->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'XII.Alam.3',
                'nama_kelas' => 'Kelas XII.Alam.3',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasXIIAlam3 ? $waliKelasXIIAlam3->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'XII.Alam.4',
                'nama_kelas' => 'Kelas XII.Alam.4',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasXIIAlam4 ? $waliKelasXIIAlam4->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'XII.Sosial.1',
                'nama_kelas' => 'Kelas XII.Sosial.1',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasXIISosial1 ? $waliKelasXIISosial1->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'XII.Sosial.2',
                'nama_kelas' => 'Kelas XII.Sosial.2',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasXIISosial2 ? $waliKelasXIISosial2->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'XII.Sosial.3',
                'nama_kelas' => 'Kelas XII.Sosial.3',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasXIISosial3 ? $waliKelasXIISosial3->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'XII.Sosial.4',
                'nama_kelas' => 'Kelas XII.Sosial.4',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasXIISosial4 ? $waliKelasXIISosial4->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'XII.Sosial.5',
                'nama_kelas' => 'Kelas XII.Sosial.5',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasXIISosial5 ? $waliKelasXIISosial5->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'XII.Sosial.6',
                'nama_kelas' => 'Kelas XII.Sosial.6',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasXIISosial6 ? $waliKelasXIISosial6->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'XII.Sosial.7',
                'nama_kelas' => 'Kelas XII.Sosial.7',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasXIISosial7 ? $waliKelasXIISosial7->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kelas' => 'XII.Bahasa.1',
                'nama_kelas' => 'Kelas XII.Bahasa.1',
                'kapasitas' => 36,
                'id_wali_kelas' => $waliKelasXIIBahasa1 ? $waliKelasXIIBahasa1->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        \DB::table('tb_kelas')->insert($kelas);
    }
}
