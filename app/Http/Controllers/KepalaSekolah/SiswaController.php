<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $filterKelas = $request->get('kelas');

        $siswaList = Siswa::aktif()->with('kelas')
            ->when($filterKelas, function ($q, $filterKelas) {
                $q->where('id_kelas', $filterKelas);
            })
            ->orderBy('nama_siswa')
            ->paginate(20);

        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('kepalasekolah.siswa.index', compact('siswaList', 'kelasList', 'filterKelas'));
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
