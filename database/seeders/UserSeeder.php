<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@spk.com',
                'password' => bcrypt('password'),
                'level' => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kepala Sekolah',
                'email' => 'kepsek@spk.com',
                'password' => bcrypt('password'),
                'level' => 'Kepala Sekolah',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Wali Kelas X',
                'email' => 'walikelas.x@spk.com',
                'password' => bcrypt('password'),
                'level' => 'Wali Kelas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Wali Kelas XI',
                'email' => 'walikelas.xi@spk.com',
                'password' => bcrypt('password'),
                'level' => 'Wali Kelas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Wali Kelas XII',
                'email' => 'walikelas.xii@spk.com',
                'password' => bcrypt('password'),
                'level' => 'Wali Kelas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        \DB::table('users')->insert($users);
    }
}
