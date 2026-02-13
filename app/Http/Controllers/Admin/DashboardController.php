<?php

namespace App\Http\Controllers\Admin;

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
        // Statistik utama
        $totalSiswa = Siswa::count();
        $totalKelas = Kelas::count();
        $totalKriteria = Kriteria::count();
        $totalPengguna = User::count();

        // Tahun ajaran aktif
        $tahunAjaranAktif = TahunAjaran::where('is_active', 1)->first();

        // Statistik penilaian pada tahun ajaran aktif
        $siswadinilai = 0;
        $siswaBelumDinilai = 0;
        $totalPelanggaran = 0;
        $hasCalculation = false;

        if ($tahunAjaranAktif) {
            $kriteriaCount = Kriteria::count();

            // Siswa yang sudah dinilai lengkap
            $siswadinilai = Penilaian::select('id_siswa')
                ->where('id_ta', $tahunAjaranAktif->id_ta)
                ->groupBy('id_siswa')
                ->havingRaw('COUNT(DISTINCT id_kriteria) = ?', [$kriteriaCount])
                ->count();

            $siswaBelumDinilai = $totalSiswa - $siswadinilai;

            // Total pelanggaran di TA aktif
            $totalPelanggaran = RiwayatPelanggaran::where('id_ta', $tahunAjaranAktif->id_ta)->count();

            // Cek apakah sudah ada perhitungan milik admin ini
            $hasCalculation = HasilAkhir::where('id_ta', $tahunAjaranAktif->id_ta)
                ->where('user_id', auth()->id())
                ->exists();
        }

        // Top 5 siswa berdasarkan SMART ranking (TA aktif)
        $topSiswa = collect();
        if ($tahunAjaranAktif && $hasCalculation) {
            $topSiswa = HasilAkhir::with(['siswa.kelas'])
                ->where('id_ta', $tahunAjaranAktif->id_ta)
                ->where('user_id', auth()->id())
                ->orderBy('rank_smart')
                ->limit(5)
                ->get();
        }

        // Distribusi siswa per kelas
        $siswaPerKelas = Kelas::withCount('siswa')
            ->orderBy('nama_kelas')
            ->get();

        // Pelanggaran terbaru (5 terakhir)
        $pelanggaranTerbaru = RiwayatPelanggaran::with(['siswa.kelas', 'jenisPelanggaran'])
            ->when($tahunAjaranAktif, function ($q) use ($tahunAjaranAktif) {
                $q->where('id_ta', $tahunAjaranAktif->id_ta);
            })
            ->orderBy('tanggal_kejadian', 'desc')
            ->limit(5)
            ->get();

        // Daftar kriteria dengan bobot
        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();
        $totalBobot = $kriteriaList->sum('bobot');

        return view('admin.dashboard', compact(
            'totalSiswa',
            'totalKelas',
            'totalKriteria',
            'totalPengguna',
            'tahunAjaranAktif',
            'siswadinilai',
            'siswaBelumDinilai',
            'totalPelanggaran',
            'hasCalculation',
            'topSiswa',
            'siswaPerKelas',
            'pelanggaranTerbaru',
            'kriteriaList',
            'totalBobot'
        ));
    }
}
