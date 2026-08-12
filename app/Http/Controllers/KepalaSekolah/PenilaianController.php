<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Semester;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    public function index(Request $request)
    {
        $activeTA = TahunAjaran::where('is_active', true)->first();
        $activeSemester = null;
        if ($activeTA) {
            $activeSemester = Semester::where('id_ta', $activeTA->id_ta)->where('is_active', true)->first()
                ?? Semester::where('id_ta', $activeTA->id_ta)->first();
        }
        if (!$activeSemester) {
            $activeSemester = Semester::where('is_active', true)->first() ?? Semester::first();
        }

        $filterTA = $request->has('tahun_ajaran') ? $request->get('tahun_ajaran') : ($activeTA?->id_ta ?? null);
        $filterSemester = $request->has('semester') ? $request->get('semester') : ($activeSemester?->id_semester ?? null);
        $filterKelas = $request->get('kelas');
        $search = $request->get('search');

        if ($filterTA && $filterSemester) {
            $semExists = Semester::where('id_semester', $filterSemester)->where('id_ta', $filterTA)->exists();
            if (!$semExists) {
                $validSem = Semester::where('id_ta', $filterTA)->where('is_active', true)->first()
                    ?? Semester::where('id_ta', $filterTA)->first();
                $filterSemester = $validSem?->id_semester;
            }
        }

        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();

        $siswaQuery = Siswa::with(['kelas', 'tahunAjaran', 'penilaian' => function ($query) use ($filterSemester) {
            if ($filterSemester) {
                $query->where('id_semester', $filterSemester);
            }
            $query->with('kriteria');
        }])
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
            'semester' => $filterSemester,
            'kelas' => $filterKelas,
            'search' => $search,
        ]);

        $tahunAjaranList = TahunAjaran::representatives()->orderBy('tahun_ajaran', 'desc')->get();
        $semesterList = Semester::orderBy('id_semester')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('kepalasekolah.penilaian.index', compact(
            'siswaList',
            'kriteriaList',
            'tahunAjaranList',
            'semesterList',
            'kelasList',
            'filterTA',
            'filterSemester',
            'filterKelas',
            'search'
        ));
    }

    public function show($id_siswa, Request $request)
    {
        $siswa = Siswa::with(['kelas', 'tahunAjaran'])->findOrFail($id_siswa);

        $filterTA = $request->get('ta', $siswa->id_ta);
        $filterSemester = $request->get('semester');

        $penilaianList = Penilaian::with('kriteria')
            ->where('id_siswa', $id_siswa)
            ->where('id_ta', $filterTA)
            ->when($filterSemester, fn($q, $s) => $q->where('id_semester', $s))
            ->get()
            ->keyBy('id_kriteria');

        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();
        $semesterList = Semester::where('id_ta', $filterTA)->get();

        return view('kepalasekolah.penilaian.show', compact('siswa', 'penilaianList', 'kriteriaList', 'semesterList', 'filterTA', 'filterSemester'));
    }
}
