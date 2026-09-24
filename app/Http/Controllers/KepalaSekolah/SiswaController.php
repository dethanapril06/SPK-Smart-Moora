<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Semester;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $activeTA = TahunAjaran::where('is_active', true)->first();
        $activeSemester = null;
        if ($activeTA) {
            $activeSemester = Semester::where('id_ta', $activeTA->id_ta)->where('is_active', true)->first()
                ?? Semester::where('id_ta', $activeTA->id_ta)->first();
        }

        $filterTA = $request->get('ta');
        $filterSemester = $request->get('semester');
        $filterKelas = $request->get('kelas');
        $search = $request->get('search');

        $siswaList = Siswa::aktif()->with(['kelas', 'tahunAjaran', 'semester'])
            ->when($filterTA, function ($q, $filterTA) {
                return $q->where('id_ta', $filterTA);
            })
            ->when($filterSemester, function ($q, $filterSemester) {
                return $q->forSemester($filterSemester);
            })
            ->when($filterKelas, function ($q, $filterKelas) {
                return $q->where('id_kelas', $filterKelas);
            })
            ->when($search, function ($q, $search) {
                return $q->where(function ($sub) use ($search) {
                    $sub->where('nisn', 'like', "%{$search}%")
                        ->orWhere('nama_siswa', 'like', "%{$search}%");
                });
            })
            ->orderBy('nama_siswa')
            ->paginate(20)
            ->appends($request->all());

        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $taList = TahunAjaran::representatives()->orderBy('tahun_ajaran', 'desc')->get();
        $semesterList = Semester::orderBy('id_semester')->get();

        return view('kepalasekolah.siswa.index', compact('siswaList', 'kelasList', 'taList', 'semesterList', 'filterKelas', 'filterTA', 'filterSemester', 'search'));
    }

    public function lulus(Request $request)
    {
        $search = $request->get('search');
        $tahunLulus = $request->get('tahun_lulus');
        $jenisKelamin = $request->get('jenis_kelamin');

        $siswa = Siswa::lulus()
            ->when($search, function ($q, $search) {
                return $q->where(function ($sub) use ($search) {
                    $sub->where('nisn', 'like', "%{$search}%")
                        ->orWhere('nama_siswa', 'like', "%{$search}%")
                        ->orWhere('alamat', 'like', "%{$search}%");
                });
            })
            ->when($tahunLulus, function ($q, $tahunLulus) {
                return $q->where('tahun_lulus', $tahunLulus);
            })
            ->when($jenisKelamin, function ($q, $jenisKelamin) {
                return $q->where('jenis_kelamin', $jenisKelamin);
            })
            ->orderBy('tahun_lulus', 'desc')
            ->orderBy('nama_siswa', 'asc')
            ->paginate(15)
            ->appends($request->all());

        $tahunLulusList = Siswa::lulus()
            ->whereNotNull('tahun_lulus')
            ->select('tahun_lulus')
            ->distinct()
            ->orderBy('tahun_lulus', 'desc')
            ->pluck('tahun_lulus');

        return view('kepalasekolah.siswa.lulus', compact(
            'siswa',
            'search',
            'tahunLulus',
            'jenisKelamin',
            'tahunLulusList'
        ));
    }

    public function exportPdfLulus(Request $request)
    {
        $search = $request->get('search');
        $tahunLulus = $request->get('tahun_lulus');
        $jenisKelamin = $request->get('jenis_kelamin');

        $siswaList = Siswa::lulus()
            ->when($search, function ($q, $search) {
                return $q->where(function ($sub) use ($search) {
                    $sub->where('nisn', 'like', "%{$search}%")
                        ->orWhere('nama_siswa', 'like', "%{$search}%")
                        ->orWhere('alamat', 'like', "%{$search}%");
                });
            })
            ->when($tahunLulus, function ($q, $tahunLulus) {
                return $q->where('tahun_lulus', $tahunLulus);
            })
            ->when($jenisKelamin, function ($q, $jenisKelamin) {
                return $q->where('jenis_kelamin', $jenisKelamin);
            })
            ->orderBy('tahun_lulus', 'desc')
            ->orderBy('nama_siswa', 'asc')
            ->get();

        $pdf = Pdf::loadView('admin.siswa.lulus_pdf', compact(
            'siswaList',
            'search',
            'tahunLulus',
            'jenisKelamin'
        ))->setPaper('a4', 'portrait');

        $filename = 'Daftar_Siswa_Lulus' . ($tahunLulus ? '_' . $tahunLulus : '') . '.pdf';

        return $pdf->download($filename);
    }
}
