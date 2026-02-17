<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NilaiEkstrakurikuler;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NilaiEkstrakurikulerController extends Controller
{
    public function index(Request $request)
    {
        $filterTA = $request->get('tahun_ajaran');
        $filterKelas = $request->get('kelas');

        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        $siswaList = collect();

        if ($filterTA && $filterKelas) {
            $siswaList = Siswa::with(['nilaiEkstrakurikuler' => function ($q) use ($filterTA) {
                $q->where('id_ta', $filterTA);
            }])
                ->where('id_kelas', $filterKelas)
                ->where('id_ta', $filterTA)
                ->orderBy('nama_siswa')
                ->get();
        }

        return view('admin.nilaiekstrakurikuler.index', compact(
            'siswaList', 'tahunAjaranList', 'kelasList',
            'filterTA', 'filterKelas'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'id_siswa' => 'required|exists:tb_siswa,id_siswa',
            'nama_ekskul' => 'required|string|max:50',
            'predikat' => 'required|in:Sangat Baik,Baik,Cukup,Kurang',
        ]);

        NilaiEkstrakurikuler::create($validated);

        return redirect()->back()->with('success', 'Nilai ekstrakurikuler berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $ekskul = NilaiEkstrakurikuler::findOrFail($id);
        $ekskul->delete();

        return redirect()->back()->with('success', 'Nilai ekstrakurikuler berhasil dihapus.');
    }
}
