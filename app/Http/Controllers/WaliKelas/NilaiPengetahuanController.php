<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\NilaiPengetahuan;
use App\Models\Siswa;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NilaiPengetahuanController extends Controller
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

        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        
        // Get mapel based on kelas relationship
        $mapelList = $kelas->mataPelajaran()->orderBy('kode_mapel')->get();

        $siswaList = collect();

        if ($filterTA) {
            $siswaList = Siswa::with(['nilaiPengetahuan' => function ($q) use ($filterTA) {
                $q->where('id_ta', $filterTA);
            }])
                ->where('id_kelas', $kelas->id_kelas)
                ->where('id_ta', $filterTA)
                ->orderBy('nama_siswa')
                ->get();
        }

        return view('walikelas.nilaipengetahuan.index', compact(
            'siswaList', 'tahunAjaranList', 'mapelList', 'filterTA', 'kelas'
        ));
    }

    public function store(Request $request)
    {
        $kelas = $this->getKelas();

        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'nilai' => 'required|array',
            'nilai.*.*' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->nilai as $id_siswa => $mapelValues) {
                // Verify student belongs to this class
                $siswa = Siswa::findOrFail($id_siswa);
                abort_if($siswa->id_kelas !== $kelas->id_kelas, 403);

                foreach ($mapelValues as $id_mapel => $nilai) {
                    if ($nilai !== null && $nilai !== '') {
                        NilaiPengetahuan::updateOrCreate(
                            [
                                'id_siswa' => $id_siswa,
                                'id_mapel' => $id_mapel,
                                'id_ta' => $validated['id_ta'],
                            ],
                            ['nilai' => $nilai]
                        );
                    }
                }
            }
            DB::commit();
            return redirect()->back()->with('success', 'Nilai pengetahuan berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage());
        }
    }
}
