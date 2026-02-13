<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::with(['waliKelas', 'siswa'])
            ->where('id_wali_kelas', auth()->id())
            ->first();

        if (!$kelas) {
            return view('walikelas.kelas.index', ['kelas' => null]);
        }

        return view('walikelas.kelas.index', compact('kelas'));
    }

    public function show(Kelas $kela)
    {
        abort_if($kela->id_wali_kelas != auth()->id(), 403, 'Bukan kelas Anda.');

        $kela->load('waliKelas', 'siswa');
        return view('walikelas.kelas.show', compact('kela'));
    }
}
