<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\HasilAkhir;
use App\Models\Kelas;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\RiwayatPelanggaran;
use App\Models\Siswa;
use App\Models\TahunAjaran;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $kelas = Kelas::where('id_wali_kelas', $user->id)->first();

        if (!$kelas) {
            return view('walikelas.dashboard', [
                'noKelas' => true,
            ]);
        }

        $kelasId = $kelas->id_kelas;

        // Statistik utama (hanya kelas miliknya)
        $totalSiswa = Siswa::where('id_kelas', $kelasId)->count();
        $totalKriteria = Kriteria::count();

        // Tahun ajaran aktif
        $tahunAjaranAktif = TahunAjaran::where('is_active', 1)->first();

        // Statistik penilaian pada tahun ajaran aktif
        $siswadinilai = 0;
        $siswaBelumDinilai = 0;
        $totalPelanggaran = 0;
        $hasCalculation = false;

        if ($tahunAjaranAktif) {
            $kriteriaCount = Kriteria::count();
            $siswaIds = Siswa::where('id_kelas', $kelasId)->pluck('id_siswa');

            // Siswa yang sudah dinilai lengkap
            $siswadinilai = Penilaian::select('id_siswa')
                ->where('id_ta', $tahunAjaranAktif->id_ta)
                ->whereIn('id_siswa', $siswaIds)
                ->groupBy('id_siswa')
                ->havingRaw('COUNT(DISTINCT id_kriteria) = ?', [$kriteriaCount])
                ->count();

            $siswaBelumDinilai = $totalSiswa - $siswadinilai;

            // Total pelanggaran di TA aktif
            $totalPelanggaran = RiwayatPelanggaran::where('id_ta', $tahunAjaranAktif->id_ta)
                ->whereIn('id_siswa', $siswaIds)
                ->count();

            // Cek apakah sudah ada perhitungan milik wali kelas ini
            $hasCalculation = HasilAkhir::where('id_ta', $tahunAjaranAktif->id_ta)
                ->where('user_id', $user->id)
                ->whereIn('id_siswa', $siswaIds)
                ->exists();
        }

        // Top 5 siswa berdasarkan SMART ranking (TA aktif, kelas saja)
        $topSmart = collect();
        $topMoora = collect();
        if ($tahunAjaranAktif && $hasCalculation) {
            $baseQuery = HasilAkhir::with(['siswa.kelas'])
                ->where('id_ta', $tahunAjaranAktif->id_ta)
                ->where('user_id', $user->id)
                ->whereHas('siswa', function ($q) use ($kelasId) {
                    $q->where('id_kelas', $kelasId);
                });

            $topSmart = (clone $baseQuery)
                ->whereNotNull('rank_smart')
                ->whereNotNull('skor_smart')
                ->orderBy('rank_smart')
                ->limit(5)
                ->get();

            $topMoora = (clone $baseQuery)
                ->whereNotNull('rank_moora')
                ->whereNotNull('skor_moora')
                ->orderBy('rank_moora')
                ->limit(5)
                ->get();
        }

        // Pelanggaran terbaru (5 terakhir, kelas saja)
        $pelanggaranTerbaru = RiwayatPelanggaran::with(['siswa', 'jenisPelanggaran'])
            ->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('id_kelas', $kelasId);
            })
            ->when($tahunAjaranAktif, function ($q) use ($tahunAjaranAktif) {
                $q->where('id_ta', $tahunAjaranAktif->id_ta);
            })
            ->orderBy('tanggal_kejadian', 'desc')
            ->limit(5)
            ->get();

        // Daftar kriteria dengan bobot
        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();
        $totalBobot = $kriteriaList->sum('bobot');

        return view('walikelas.dashboard', compact(
            'kelas',
            'totalSiswa',
            'totalKriteria',
            'tahunAjaranAktif',
            'siswadinilai',
            'siswaBelumDinilai',
            'totalPelanggaran',
            'hasCalculation',
            'topSmart',
            'topMoora',
            'pelanggaranTerbaru',
            'kriteriaList',
            'totalBobot'
        ));
    }
}
