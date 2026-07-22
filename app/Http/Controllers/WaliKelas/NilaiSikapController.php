<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\NilaiSikap;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NilaiSikapController extends Controller
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

        if (!$filterTA) {
            $activeTA = TahunAjaran::where('is_active', 1)->first();
            $filterTA = $activeTA ? $activeTA->id_ta : null;
        }

        if (!$filterSemester) {
            $activeSemester = Semester::where('is_active', 1)->first();
            $filterSemester = $activeSemester ? $activeSemester->id_semester : null;
        }

        $tahunAjaranList = TahunAjaran::representatives()->orderBy('tahun_ajaran', 'desc')->get();
        $semesterList = Semester::orderBy('id_semester')->get();
        $predikatList = ['Sangat Baik', 'Baik', 'Cukup', 'Kurang'];

        $siswaList = collect();

        if ($filterTA && $filterSemester) {
            $siswaList = Siswa::with(['nilaiSikap' => function ($q) use ($filterTA, $filterSemester) {
                $q->where('id_ta', $filterTA)->where('id_semester', $filterSemester);
            }])
                ->where('id_kelas', $kelas->id_kelas)
                ->where('id_ta', $filterTA)
                ->orderBy('nama_siswa')
                ->get();
        }

        return view('walikelas.nilaisikap.index', compact(
            'siswaList', 'tahunAjaranList', 'semesterList', 'predikatList', 'filterTA', 'filterSemester', 'kelas'
        ));
    }

    public function store(Request $request)
    {
        $kelas = $this->getKelas();

        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'id_semester' => 'required|exists:tb_semester,id_semester',
            'sikap' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->sikap as $id_siswa => $values) {
                $siswa = Siswa::findOrFail($id_siswa);
                abort_if($siswa->id_kelas !== $kelas->id_kelas, 403);

                if (!empty($values['spiritual']) && !empty($values['sosial'])) {
                    NilaiSikap::updateOrCreate(
                        [
                            'id_siswa' => $id_siswa,
                            'id_ta' => $validated['id_ta'],
                            'id_semester' => $validated['id_semester'],
                        ],
                        [
                            'sikap_spiritual' => $values['spiritual'],
                            'sikap_sosial' => $values['sosial'],
                        ]
                    );
                }
            }
            DB::commit();
            return redirect()->back()->with('success', 'Nilai sikap berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage());
        }
    }
}
