<?php

namespace App\Http\Controllers\WaliKelas;

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

        $siswaList = collect();

        if ($filterTA && $filterSemester) {
            $siswaList = Siswa::with(['nilaiAbsensi' => function ($q) use ($filterTA, $filterSemester) {
                $q->where('id_ta', $filterTA)->where('id_semester', $filterSemester);
            }])
                ->where('id_kelas', $kelas->id_kelas)
                ->where('id_ta', $filterTA)
                ->orderBy('nama_siswa')
                ->get();
        }

        return view('walikelas.nilaiabsensi.index', compact(
            'siswaList', 'tahunAjaranList', 'semesterList', 'filterTA', 'filterSemester', 'kelas'
        ));
    }

    public function store(Request $request)
    {
        $kelas = $this->getKelas();

        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'id_semester' => 'required|exists:tb_semester,id_semester',
            'absensi' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->absensi as $id_siswa => $values) {
                $siswa = Siswa::findOrFail($id_siswa);
                abort_if($siswa->id_kelas !== $kelas->id_kelas, 403);

                NilaiAbsensi::updateOrCreate(
                    [
                        'id_siswa' => $id_siswa,
                        'id_ta' => $validated['id_ta'],
                        'id_semester' => $validated['id_semester'],
                    ],
                    [
                        'jumlah_sakit' => $values['sakit'] ?? 0,
                        'jumlah_izin' => $values['izin'] ?? 0,
                        'jumlah_alpa' => $values['alpa'] ?? 0,
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
