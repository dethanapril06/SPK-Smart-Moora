<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    public function index(Request $request)
    {
        $filterTA = $request->get('tahun_ajaran');
        $filterKelas = $request->get('kelas');
        $search = $request->get('search');

        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();

        $siswaQuery = Siswa::with(['kelas', 'tahunAjaran', 'penilaian.kriteria'])
            ->when($filterTA, function ($query, $filterTA) {
                return $query->where('id_ta', $filterTA);
            })
            ->when($filterKelas, function ($query, $filterKelas) {
                return $query->where('id_kelas', $filterKelas);
            })
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nama_siswa', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%");
                });
            });

        $siswaList = $siswaQuery->paginate(10)->appends([
            'tahun_ajaran' => $filterTA,
            'kelas' => $filterKelas,
            'search' => $search,
        ]);

        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('kepalasekolah.penilaian.index', compact(
            'siswaList',
            'kriteriaList',
            'tahunAjaranList',
            'kelasList',
            'filterTA',
            'filterKelas',
            'search'
        ));
    }

    public function show($id_siswa, Request $request)
    {
        $siswa = Siswa::with(['kelas', 'tahunAjaran'])->findOrFail($id_siswa);

        $filterTA = $request->get('ta', $siswa->id_ta);

        $penilaianList = Penilaian::with('kriteria')
            ->where('id_siswa', $id_siswa)
            ->where('id_ta', $filterTA)
            ->get()
            ->keyBy('id_kriteria');

        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();

        return view('kepalasekolah.penilaian.show', compact('siswa', 'penilaianList', 'kriteriaList', 'filterTA'));
    }
}
