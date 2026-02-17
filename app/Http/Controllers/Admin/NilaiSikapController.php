<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NilaiSikap;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NilaiSikapController extends Controller
{
    public function index(Request $request)
    {
        $filterTA = $request->get('tahun_ajaran');
        $filterKelas = $request->get('kelas');

        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        $siswaList = collect();

        if ($filterTA && $filterKelas) {
            $siswaList = Siswa::with(['nilaiSikap' => function ($q) use ($filterTA) {
                $q->where('id_ta', $filterTA);
            }])
                ->where('id_kelas', $filterKelas)
                ->where('id_ta', $filterTA)
                ->orderBy('nama_siswa')
                ->get();
        }

        return view('admin.nilaisikap.index', compact(
            'siswaList', 'tahunAjaranList', 'kelasList',
            'filterTA', 'filterKelas'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'id_kelas' => 'required',
            'sikap_spiritual' => 'required|array',
            'sikap_spiritual.*' => 'required|in:Sangat Baik,Baik,Cukup,Kurang',
            'sikap_sosial' => 'required|array',
            'sikap_sosial.*' => 'required|in:Sangat Baik,Baik,Cukup,Kurang',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->sikap_spiritual as $id_siswa => $spiritual) {
                NilaiSikap::updateOrCreate(
                    [
                        'id_siswa' => $id_siswa,
                        'id_ta' => $validated['id_ta'],
                    ],
                    [
                        'sikap_spiritual' => $spiritual,
                        'sikap_sosial' => $request->sikap_sosial[$id_siswa],
                    ]
                );
            }
            DB::commit();
            return redirect()->back()->with('success', 'Nilai sikap berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage());
        }
    }
}
