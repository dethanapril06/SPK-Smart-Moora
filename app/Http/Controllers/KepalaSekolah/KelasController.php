<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Kelas;

class KelasController extends Controller
{
    public function index()
    {
        $kelasList = Kelas::withCount('siswa')
            ->with('waliKelas')
            ->orderBy('nama_kelas')
            ->get();

        return view('kepalasekolah.kelas.index', compact('kelasList'));
    }
}
