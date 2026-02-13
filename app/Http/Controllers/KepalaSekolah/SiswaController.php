<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $filterKelas = $request->get('kelas');

        $siswaList = Siswa::with('kelas')
            ->when($filterKelas, function ($q, $filterKelas) {
                $q->where('id_kelas', $filterKelas);
            })
            ->orderBy('nama_siswa')
            ->paginate(20);

        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('kepalasekolah.siswa.index', compact('siswaList', 'kelasList', 'filterKelas'));
    }
}
