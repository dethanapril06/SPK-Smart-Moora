<?php

namespace App\Http\Controllers\WaliKelas;

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
    protected function getKelas()
    {
        $kelas = Kelas::where('id_wali_kelas', auth()->id())->first();
        abort_if(!$kelas, 403, 'Anda belum ditugaskan sebagai wali kelas.');
        return $kelas;
    }

    public function index(Request $request)
    {
        $kelas = $this->getKelas();
        $filterTA = $request->get('tahun_ajaran');
        $filterSemester = $request->get('semester');

        $tahunAjaranList = TahunAjaran::representatives()->orderBy('tahun_ajaran', 'desc')->get();
        $semesterList = Semester::orderBy('id_semester')->get();
        
        $mapelList = MataPelajaran::orderBy('kode_mapel')->get();

        $siswaList = collect();

        if ($filterTA && $filterSemester) {
            $siswaList = Siswa::with(['nilaiKeterampilan' => function ($q) use ($filterTA, $filterSemester) {
                $q->where('id_ta', $filterTA)->where('id_semester', $filterSemester);
            }])
                ->where('id_kelas', $kelas->id_kelas)
                ->where('id_ta', $filterTA)
                ->orderBy('nama_siswa')
                ->get();
        }

        return view('walikelas.nilaiketerampilan.index', compact(
            'siswaList', 'tahunAjaranList', 'semesterList', 'mapelList', 'filterTA', 'filterSemester', 'kelas'
        ));
    }

    public function store(Request $request)
    {
        $kelas = $this->getKelas();

        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'id_semester' => 'required|exists:tb_semester,id_semester',
            'nilai' => 'required|array',
            'nilai.*.*' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->nilai as $id_siswa => $mapelValues) {
                $siswa = Siswa::findOrFail($id_siswa);
                abort_if($siswa->id_kelas !== $kelas->id_kelas, 403);

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
