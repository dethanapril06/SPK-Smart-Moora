<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NilaiAbsensi;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NilaiAbsensiController extends Controller
{
    public function index(Request $request)
    {
        $filterTA = $request->get('tahun_ajaran');
        $filterSemester = $request->get('semester');
        $filterKelas = $request->get('kelas');

        $tahunAjaranList = TahunAjaran::representatives()->orderBy('tahun_ajaran', 'desc')->get();
        $semesterList = Semester::orderBy('id_semester')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        $siswaList = collect();

        if ($filterTA && $filterSemester && $filterKelas) {
            $siswaList = Siswa::with(['nilaiAbsensi' => function ($q) use ($filterTA, $filterSemester) {
                $q->where('id_ta', $filterTA)->where('id_semester', $filterSemester);
            }])
                ->where('id_kelas', $filterKelas)
                ->where('id_ta', $filterTA)
                ->orderBy('nama_siswa')
                ->get();
        }

        return view('admin.nilaiabsensi.index', compact(
            'siswaList', 'tahunAjaranList', 'semesterList', 'kelasList',
            'filterTA', 'filterSemester', 'filterKelas'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'id_semester' => 'required|exists:tb_semester,id_semester',
            'id_kelas' => 'required',
            'jumlah_sakit' => 'required|array',
            'jumlah_sakit.*' => 'nullable|integer|min:0',
            'jumlah_izin' => 'required|array',
            'jumlah_izin.*' => 'nullable|integer|min:0',
            'jumlah_alpa' => 'required|array',
            'jumlah_alpa.*' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->jumlah_sakit as $id_siswa => $sakit) {
                NilaiAbsensi::updateOrCreate(
                    [
                        'id_siswa' => $id_siswa,
                        'id_ta' => $validated['id_ta'],
                        'id_semester' => $validated['id_semester'],
                    ],
                    [
                        'jumlah_sakit' => $sakit ?? 0,
                        'jumlah_izin' => $request->jumlah_izin[$id_siswa] ?? 0,
                        'jumlah_alpa' => $request->jumlah_alpa[$id_siswa] ?? 0,
                    ]
                );
            }
            DB::commit();
            return redirect()->back()->with('success', 'Data absensi berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }
}
