<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NilaiAbsensi;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NilaiAbsensiController extends Controller
{
    public function index(Request $request)
    {
        $filterTA = $request->get('tahun_ajaran');
        $filterKelas = $request->get('kelas');

        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        $siswaList = collect();

        if ($filterTA && $filterKelas) {
            $siswaList = Siswa::with(['nilaiAbsensi' => function ($q) use ($filterTA) {
                $q->where('id_ta', $filterTA);
            }])
                ->where('id_kelas', $filterKelas)
                ->where('id_ta', $filterTA)
                ->orderBy('nama_siswa')
                ->get();
        }

        return view('admin.nilaiabsensi.index', compact(
            'siswaList', 'tahunAjaranList', 'kelasList',
            'filterTA', 'filterKelas'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
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
