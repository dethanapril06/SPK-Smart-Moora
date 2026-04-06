<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NilaiPengetahuan;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NilaiPengetahuanController extends Controller
{
    public function index(Request $request)
    {
        $filterTA = $request->get('tahun_ajaran');
        $filterKelas = $request->get('kelas');

        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        
        // Get mapel based on selected kelas relationship
        $mapelList = collect();
        if ($filterKelas) {
            $kelas = Kelas::with('mataPelajaran')->find($filterKelas);
            if ($kelas) {
                $mapelList = $kelas->mataPelajaran()->orderBy('kode_mapel')->get();
            }
        }

        $siswaList = collect();

        if ($filterTA && $filterKelas) {
            $siswaList = Siswa::with(['nilaiPengetahuan' => function ($q) use ($filterTA) {
                $q->where('id_ta', $filterTA);
            }])
                ->where('id_kelas', $filterKelas)
                ->where('id_ta', $filterTA)
                ->orderBy('nama_siswa')
                ->get();
        }

        return view('admin.nilaipengetahuan.index', compact(
            'siswaList', 'tahunAjaranList', 'kelasList', 'mapelList',
            'filterTA', 'filterKelas'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'id_kelas' => 'required',
            'nilai' => 'required|array',
            'nilai.*.*' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->nilai as $id_siswa => $mapelValues) {
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
