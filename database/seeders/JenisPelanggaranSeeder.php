<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JenisPelanggaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kategori: Keterlambatan
        $keterlambatan = [
            [
                'kategori_pelanggaran' => 'Keterlambatan',
                'nama_pelanggaran' => 'Terlambat mengikuti apel dan doa pagi bersama',
                'bobot_poin' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Keterlambatan',
                'nama_pelanggaran' => 'Terlambat > 5 (lima) menit pada jam pelajaran',
                'bobot_poin' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Keterlambatan',
                'nama_pelanggaran' => 'Terlambat masuk kelas setelah pergantian jam',
                'bobot_poin' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Keterlambatan',
                'nama_pelanggaran' => 'Terlambat mengikuti kegiatan studi, kerja ekstrakulikuler pada jam 06.45',
                'bobot_poin' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Keterlambatan',
                'nama_pelanggaran' => 'Terlambat mengikuti upacara bendera hari senin pada jam 06.45',
                'bobot_poin' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        \DB::table('tb_jenis_pelanggaran')->insert($keterlambatan);

        // Kategori: Kehadiran
        $kehadiran = [
            [
                'kategori_pelanggaran' => 'Kehadiran',
                'nama_pelanggaran' => 'Tidak hadir dalam kegiatan pembelajaran karena alpa',
                'bobot_poin' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Kehadiran',
                'nama_pelanggaran' => 'Tidak hadir dalam kegiatan-kegiatan yang diwajibkan',
                'bobot_poin' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Kehadiran',
                'nama_pelanggaran' => 'Kabur dari kelas (bolos)',
                'bobot_poin' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Kehadiran',
                'nama_pelanggaran' => 'Kabur (bolos) dari kegiatan-kegiatan yang diwajibkan sekolah',
                'bobot_poin' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        \DB::table('tb_jenis_pelanggaran')->insert($kehadiran);

        // Kategori: Pakaian
        $pakaian = [
            [
                'kategori_pelanggaran' => 'Pakaian',
                'nama_pelanggaran' => 'Tidak mengenakan pakaian seragam sesuai ketentuan yang berlaku - Celana Panjang',
                'bobot_poin' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Pakaian',
                'nama_pelanggaran' => 'Tidak mengenakan pakaian seragam sesuai ketentuan yang berlaku - Rok',
                'bobot_poin' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Pakaian',
                'nama_pelanggaran' => 'Tidak mengenakan pakaian seragam sesuai ketentuan yang berlaku - Kemeja',
                'bobot_poin' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Pakaian',
                'nama_pelanggaran' => 'Tidak mengenakan pakaian seragam sesuai ketentuan yang berlaku - Ikat pinggang',
                'bobot_poin' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Pakaian',
                'nama_pelanggaran' => 'Tidak mengenakan pakaian seragam sesuai ketentuan yang berlaku - Kaos kaki',
                'bobot_poin' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Pakaian',
                'nama_pelanggaran' => 'Tidak mengenakan pakaian seragam sesuai ketentuan yang berlaku - Sepatu',
                'bobot_poin' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Pakaian',
                'nama_pelanggaran' => 'Memakai celana panjang tebelah samping dan terinjak bagian bawah (terlalu lebar bagian bawah)',
                'bobot_poin' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Pakaian',
                'nama_pelanggaran' => 'Tidak memakai seragam olahraga pada waktu mengikuti kegiatan olahraga',
                'bobot_poin' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Pakaian',
                'nama_pelanggaran' => 'Memakai jaket di ruang kelas / di lingkungan sekolah selama hari dan jam efektif sekolah (kecuali sakit)',
                'bobot_poin' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Pakaian',
                'nama_pelanggaran' => 'Baju dikeluarkan / tidak rapi',
                'bobot_poin' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Pakaian',
                'nama_pelanggaran' => 'Rok ketat dan terbelah samping',
                'bobot_poin' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Pakaian',
                'nama_pelanggaran' => 'Memakai aksesoris lain yang tidak sesuai dengan ketentuan sekolah',
                'bobot_poin' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        \DB::table('tb_jenis_pelanggaran')->insert($pakaian);

        // Kategori: Kelakuan
        $kelakuan = [
            [
                'kategori_pelanggaran' => 'Kelakuan',
                'nama_pelanggaran' => 'Menggunakan cat kuku, pewarna rambut yang aneh, rambut palsu, lipstik, eyeshadow',
                'bobot_poin' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Kelakuan',
                'nama_pelanggaran' => 'Mengenakan perhiasan (gelang kalung, anting)',
                'bobot_poin' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Kelakuan',
                'nama_pelanggaran' => 'Rambut menutupi kerah kemeja/ telinga untuk siswa putra',
                'bobot_poin' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Kelakuan',
                'nama_pelanggaran' => 'Mengeluarkan kata-kata kotor/maki, baik di depan guru, pegawai, karyawan, sesama teman di dalam lingkungan sekolah',
                'bobot_poin' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Kelakuan',
                'nama_pelanggaran' => 'Mengancam, mengintimidasi guru, pegawai, karyawan, sesame teman',
                'bobot_poin' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Kelakuan',
                'nama_pelanggaran' => 'Melakukan pemerasan di dalam lingkungan sekolah',
                'bobot_poin' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Kelakuan',
                'nama_pelanggaran' => 'Melakukan tindakan pencurian di dalam lingkungan sekolah',
                'bobot_poin' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Kelakuan',
                'nama_pelanggaran' => 'Berkelahi dengan teman sendiri',
                'bobot_poin' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Kelakuan',
                'nama_pelanggaran' => 'Berkelahi dengan siswa dari sekolah lain',
                'bobot_poin' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Kelakuan',
                'nama_pelanggaran' => 'Mencemarkan nama baik sekolah',
                'bobot_poin' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Kelakuan',
                'nama_pelanggaran' => 'Menggunakan seragam sekolah melakukan tindakan kejahatan',
                'bobot_poin' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Kelakuan',
                'nama_pelanggaran' => 'Siswi terbukti hamil',
                'bobot_poin' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Kelakuan',
                'nama_pelanggaran' => 'Siswa terbukti menghamili orang',
                'bobot_poin' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Kelakuan',
                'nama_pelanggaran' => 'Terbukti melakukan tindakan kriminal',
                'bobot_poin' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Kelakuan',
                'nama_pelanggaran' => 'Membawa HP ke sekolah tanpa izin / sepengetahuan sekolah',
                'bobot_poin' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Kelakuan',
                'nama_pelanggaran' => 'Membentuk gang/ kelompok yang bersifat destruktif',
                'bobot_poin' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Kelakuan',
                'nama_pelanggaran' => 'Tawuran antar sekolah/ siswa sebagai pihak yang bersalah',
                'bobot_poin' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Kelakuan',
                'nama_pelanggaran' => 'Buang air kecil di lorong, ruang kelas dan ruangan lain',
                'bobot_poin' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Kelakuan',
                'nama_pelanggaran' => 'Menuding dan memaki guru, pegawai dan karyawan',
                'bobot_poin' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Kelakuan',
                'nama_pelanggaran' => 'Menyalagunakan uang sekolah pemberian orang tua untuk judi, merokok, membeli dan mengkomsumsi minuman keras',
                'bobot_poin' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        \DB::table('tb_jenis_pelanggaran')->insert($kelakuan);

        // Kategori: Ketertiban
        $ketertiban = [
            [
                'kategori_pelanggaran' => 'Ketertiban',
                'nama_pelanggaran' => 'Membawa majalah, kaset, menggambar di dinding sekolah, meja, kursi (sarana sekolah)',
                'bobot_poin' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Ketertiban',
                'nama_pelanggaran' => 'Merusak barang-barang milik sekolah dan milik orang lain (teman, guru, pegawai dan karyawan)',
                'bobot_poin' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Ketertiban',
                'nama_pelanggaran' => 'Membuat kegaduhan dan keributan di ruang kelas pada jam sekolah belajar',
                'bobot_poin' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Ketertiban',
                'nama_pelanggaran' => 'Melompat lewat jendela',
                'bobot_poin' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Ketertiban',
                'nama_pelanggaran' => 'Makan dan minum di kelas baik pada saat Pelajaran berlangsung maupun pada sat istirahat',
                'bobot_poin' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Ketertiban',
                'nama_pelanggaran' => 'Tidak membawa buku tata tertib dan kartu penilaian ke sekolah',
                'bobot_poin' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Ketertiban',
                'nama_pelanggaran' => 'Membawa kosmetik (bedak, lipstick,dll) ke dalam ruang kelas pada jam-jam pelajaran',
                'bobot_poin' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Ketertiban',
                'nama_pelanggaran' => 'Tidak membawa buku pelajaran dan alat tulis yang diwajibkan',
                'bobot_poin' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Ketertiban',
                'nama_pelanggaran' => 'Mengabaikan panggilan sekolah',
                'bobot_poin' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Ketertiban',
                'nama_pelanggaran' => 'Keluar dari kelas pada jam pelajaran tanpa izin dari guru yang mengajar',
                'bobot_poin' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Ketertiban',
                'nama_pelanggaran' => 'Membuang sampah tidak pada tempatnya',
                'bobot_poin' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Ketertiban',
                'nama_pelanggaran' => 'Pindah tempat duduk tidak sesuai dengan denah kelas',
                'bobot_poin' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        \DB::table('tb_jenis_pelanggaran')->insert($ketertiban);

        // Kategori: Kerajinan
        $kerajinan = [
            [
                'kategori_pelanggaran' => 'Kerajinan',
                'nama_pelanggaran' => 'Tidak mengerjakan tugas sekolah / PR dari guru',
                'bobot_poin' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Kerajinan',
                'nama_pelanggaran' => 'Tidak melaksanakan tugas kelas/ piket kelas',
                'bobot_poin' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Kerajinan',
                'nama_pelanggaran' => 'Mengabaikan sanksi guru',
                'bobot_poin' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        \DB::table('tb_jenis_pelanggaran')->insert($kerajinan);

        // Kategori: Narkoba_Miras
        $narkoba_miras = [
            [
                'kategori_pelanggaran' => 'Narkoba_Miras',
                'nama_pelanggaran' => 'Membawa dan mengedarkan narkoba maupun minuman keras di lingkungan sekolah',
                'bobot_poin' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Narkoba_Miras',
                'nama_pelanggaran' => 'Mengkonsumsi narkoba dan minum-minuman keras di dalam lingkungan sekolah',
                'bobot_poin' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        \DB::table('tb_jenis_pelanggaran')->insert($narkoba_miras);

        // Kategori: Tata_Tertib_Ujian
        $tata_tertib_ujian = [
            [
                'kategori_pelanggaran' => 'Tata_Tertib_Ujian',
                'nama_pelanggaran' => 'Mencontek, menanyakan jawaban siswa lain (berbuat curang)',
                'bobot_poin' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Tata_Tertib_Ujian',
                'nama_pelanggaran' => 'Memanfaatkan HP pada saat ulangan/ ujian',
                'bobot_poin' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Tata_Tertib_Ujian',
                'nama_pelanggaran' => 'Merusakkan dan menghilangkan kartu ujian pada saat ujian berlangsung',
                'bobot_poin' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Tata_Tertib_Ujian',
                'nama_pelanggaran' => 'Tidak membawa kartu ujian pada saat ujian berlangsung',
                'bobot_poin' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Tata_Tertib_Ujian',
                'nama_pelanggaran' => 'Membuat gaduh dalam ruang ujian selama ujian/ ulangan berlangsung',
                'bobot_poin' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_pelanggaran' => 'Tata_Tertib_Ujian',
                'nama_pelanggaran' => 'Menepati tempat duduk tidak sesuai nomor ujian yang telah ditentukan',
                'bobot_poin' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        \DB::table('tb_jenis_pelanggaran')->insert($tata_tertib_ujian);
    }
}
