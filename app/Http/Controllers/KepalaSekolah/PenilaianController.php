<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    public function index(Request $request)
    {
        $filterTA = $request->get('tahun_ajaran');
        $filterKelas = $request->get('kelas');
        $tahunAjaranAktif = TahunAjaran::where('is_active', 1)->first();

        if (!$filterTA && $tahunAjaranAktif) {
            $filterTA = $tahunAjaranAktif->id_ta;
        }

        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();

        $penilaianList = collect();
        if ($filterTA) {
            $penilaianQuery = Penilaian::with(['siswa.kelas', 'kriteria'])
                ->where('id_ta', $filterTA)
                ->when($filterKelas, function ($q, $filterKelas) {
                    $q->whereHas('siswa', function ($sq) use ($filterKelas) {
                        $sq->where('id_kelas', $filterKelas);
                    });
                });

            $penilaianList = $penilaianQuery->get()->groupBy('id_siswa');
        }

        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('kepalasekolah.penilaian.index', compact(
            'penilaianList',
            'kriteriaList',
            'tahunAjaranList',
            'kelasList',
            'filterTA',
            'filterKelas'
        ));
    }
}
