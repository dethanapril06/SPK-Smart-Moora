<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\HasilAkhir;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;

class PerhitunganController extends Controller
{
    /**
     * Tampilkan hasil perangkingan dari admin (semua kelas)
     * dan dari masing-masing wali kelas (per kelas).
     */
    public function index(Request $request)
    {
        $filterTA = $request->get('tahun_ajaran');
        $source = $request->get('source', 'admin'); // 'admin' or user_id wali kelas
        $filterKelas = $request->get('kelas');
        $tahunAjaranAktif = TahunAjaran::where('is_active', 1)->first();

        if (!$filterTA && $tahunAjaranAktif) {
            $filterTA = $tahunAjaranAktif->id_ta;
        }

        $hasilList = collect();
        $sourceName = 'Admin';
        $hasCalculation = false;

        if ($filterTA) {
            if ($source === 'admin') {
                // Ambil dari admin: cari user dengan level Admin yang punya hasil
                $adminUser = User::where('level', 'Admin')->first();
                if ($adminUser) {
                    $hasilQuery = HasilAkhir::with(['siswa.kelas', 'tahunAjaran'])
                        ->where('id_ta', $filterTA)
                        ->where('user_id', $adminUser->id)
                        ->when($filterKelas, function ($q, $filterKelas) {
                            $q->whereHas('siswa', function ($sq) use ($filterKelas) {
                                $sq->where('id_kelas', $filterKelas);
                            });
                        });
                    $hasilList = $hasilQuery->get()->sortBy('rank_smart');
                }
                $sourceName = 'Admin (Semua Siswa)';
            } else {
                // Ambil dari wali kelas tertentu
                $waliKelas = User::where('level', 'Wali Kelas')->find($source);
                if ($waliKelas) {
                    $kelas = Kelas::where('id_wali_kelas', $waliKelas->id)->first();
                    $hasilQuery = HasilAkhir::with(['siswa.kelas', 'tahunAjaran'])
                        ->where('id_ta', $filterTA)
                        ->where('user_id', $waliKelas->id);
                    $hasilList = $hasilQuery->get()->sortBy('rank_smart');
                    $sourceName = 'Wali Kelas: ' . $waliKelas->name . ($kelas ? ' (' . $kelas->nama_kelas . ')' : '');
                }
            }

            $hasCalculation = $hasilList->count() > 0;
        }

        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        // Daftar sumber perhitungan (admin + wali kelas yang punya hasil)
        $sourceList = $this->getSourceList($filterTA);

        return view('kepalasekolah.perhitungan.index', compact(
            'hasilList',
            'tahunAjaranList',
            'kelasList',
            'filterTA',
            'filterKelas',
            'source',
            'sourceName',
            'hasCalculation',
            'sourceList'
        ));
    }

    /**
     * Bandingkan SMART vs MOORA
     */
    public function compare($id_ta, Request $request)
    {
        $source = $request->get('source', 'admin');
        $tahunAjaran = TahunAjaran::findOrFail($id_ta);

        $userId = $this->resolveUserId($source);

        $hasilList = HasilAkhir::with('siswa')
            ->where('id_ta', $id_ta)
            ->when($userId, function ($q, $userId) {
                $q->where('user_id', $userId);
            })
            ->get();

        $agreement = 0;
        foreach ($hasilList as $hasil) {
            if ($hasil->rank_smart == $hasil->rank_moora) {
                $agreement++;
            }
        }

        $agreementPercentage = $hasilList->count() > 0
            ? round(($agreement / $hasilList->count()) * 100, 2)
            : 0;

        $sourceName = $this->resolveSourceName($source);

        return view('kepalasekolah.perhitungan.compare', compact(
            'tahunAjaran',
            'hasilList',
            'agreementPercentage',
            'source',
            'sourceName'
        ));
    }

    /**
     * Mendapatkan daftar sumber perhitungan yang tersedia
     */
    protected function getSourceList($filterTA)
    {
        $sources = [];

        if (!$filterTA) return $sources;

        // Cek admin
        $adminUser = User::where('level', 'Admin')->first();
        if ($adminUser) {
            $adminHasResult = HasilAkhir::where('id_ta', $filterTA)
                ->where('user_id', $adminUser->id)
                ->exists();
            if ($adminHasResult) {
                $sources[] = [
                    'value' => 'admin',
                    'label' => 'Admin (Semua Siswa)',
                ];
            }
        }

        // Cek setiap wali kelas
        $waliKelasList = User::where('level', 'Wali Kelas')->get();
        foreach ($waliKelasList as $wk) {
            $hasResult = HasilAkhir::where('id_ta', $filterTA)
                ->where('user_id', $wk->id)
                ->exists();
            if ($hasResult) {
                $kelas = Kelas::where('id_wali_kelas', $wk->id)->first();
                $sources[] = [
                    'value' => $wk->id,
                    'label' => $wk->name . ($kelas ? ' (' . $kelas->nama_kelas . ')' : ''),
                ];
            }
        }

        return $sources;
    }

    protected function resolveUserId($source)
    {
        if ($source === 'admin') {
            $admin = User::where('level', 'Admin')->first();
            return $admin ? $admin->id : null;
        }
        return (int) $source;
    }

    protected function resolveSourceName($source)
    {
        if ($source === 'admin') {
            return 'Admin (Semua Siswa)';
        }
        $user = User::find($source);
        if ($user) {
            $kelas = Kelas::where('id_wali_kelas', $user->id)->first();
            return 'Wali Kelas: ' . $user->name . ($kelas ? ' (' . $kelas->nama_kelas . ')' : '');
        }
        return 'Tidak Diketahui';
    }
}
