<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\HasilAkhir;
use App\Models\Kelas;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\RiwayatPelanggaran;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSiswa = Siswa::count();
        $totalKelas = Kelas::count();
        $totalKriteria = Kriteria::count();

        $tahunAjaranAktif = TahunAjaran::where('is_active', 1)->first();

        $siswadinilai = 0;
        $siswaBelumDinilai = 0;
        $totalPelanggaran = 0;

        if ($tahunAjaranAktif) {
            $kriteriaCount = Kriteria::count();

            $siswadinilai = Penilaian::select('id_siswa')
                ->where('id_ta', $tahunAjaranAktif->id_ta)
                ->groupBy('id_siswa')
                ->havingRaw('COUNT(DISTINCT id_kriteria) = ?', [$kriteriaCount])
                ->count();

            $siswaBelumDinilai = $totalSiswa - $siswadinilai;

            $totalPelanggaran = RiwayatPelanggaran::where('id_ta', $tahunAjaranAktif->id_ta)->count();
        }

        // Jumlah wali kelas yang sudah melakukan perhitungan
        $waliKelasCount = 0;
        $adminCount = 0;
        if ($tahunAjaranAktif) {
            $waliKelasCount = HasilAkhir::where('id_ta', $tahunAjaranAktif->id_ta)
                ->whereHas('user', function ($q) {
                    $q->where('level', 'Wali Kelas');
                })
                ->distinct('user_id')
                ->count('user_id');

            $adminCount = HasilAkhir::where('id_ta', $tahunAjaranAktif->id_ta)
                ->whereHas('user', function ($q) {
                    $q->where('level', 'Admin');
                })
                ->distinct('user_id')
                ->count('user_id');
        }

        $siswaPerKelas = Kelas::withCount('siswa')
            ->orderBy('nama_kelas')
            ->get();

        $pelanggaranTerbaru = RiwayatPelanggaran::with(['siswa.kelas', 'jenisPelanggaran'])
            ->when($tahunAjaranAktif, function ($q) use ($tahunAjaranAktif) {
                $q->where('id_ta', $tahunAjaranAktif->id_ta);
            })
            ->orderBy('tanggal_kejadian', 'desc')
            ->limit(5)
            ->get();

        return view('kepalasekolah.dashboard', compact(
            'totalSiswa',
            'totalKelas',
            'totalKriteria',
            'tahunAjaranAktif',
            'siswadinilai',
            'siswaBelumDinilai',
            'totalPelanggaran',
            'waliKelasCount',
            'adminCount',
            'siswaPerKelas',
            'pelanggaranTerbaru'
        ));
    }
}
