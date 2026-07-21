<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NilaiKeterampilan;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NilaiKeterampilanController extends Controller
{
    public function index(Request $request)
    {
        $filterTA = $request->get('tahun_ajaran');
        $filterSemester = $request->get('semester');
        $filterKelas = $request->get('kelas');

        $tahunAjaranList = TahunAjaran::representatives()->orderBy('tahun_ajaran', 'desc')->get();
        $semesterList = Semester::orderBy('id_semester')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        
        $mapelList = $filterKelas
            ? MataPelajaran::orderBy('kode_mapel')->get()
            : collect();

        $siswaList = collect();

        if ($filterTA && $filterSemester && $filterKelas) {
            $siswaList = Siswa::with(['nilaiKeterampilan' => function ($q) use ($filterTA, $filterSemester) {
                $q->where('id_ta', $filterTA)->where('id_semester', $filterSemester);
            }])
                ->where('id_kelas', $filterKelas)
                ->where('id_ta', $filterTA)
                ->orderBy('nama_siswa')
                ->get();
        }

        return view('admin.nilaiketerampilan.index', compact(
            'siswaList', 'tahunAjaranList', 'semesterList', 'kelasList', 'mapelList',
            'filterTA', 'filterSemester', 'filterKelas'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'id_semester' => 'required|exists:tb_semester,id_semester',
            'id_kelas' => 'required',
            'nilai' => 'required|array',
            'nilai.*.*' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->nilai as $id_siswa => $mapelValues) {
                foreach ($mapelValues as $id_mapel => $nilai) {
                    if ($nilai !== null && $nilai !== '') {
                        NilaiKeterampilan::updateOrCreate(
                            [
                                'id_siswa' => $id_siswa,
                                'id_mapel' => $id_mapel,
                                'id_ta' => $validated['id_ta'],
                                'id_semester' => $validated['id_semester'],
                            ],
                            ['nilai' => $nilai]
                        );
                    }
                }
            }
            DB::commit();
            return redirect()->back()->with('success', 'Nilai keterampilan berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage());
        }
    }
}
