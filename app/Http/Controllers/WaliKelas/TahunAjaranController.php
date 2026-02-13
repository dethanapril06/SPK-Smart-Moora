<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $tahunAjaran = TahunAjaran::query()
            ->when($search, function ($query, $search) {
                return $query->where('tahun_ajaran', 'like', "%{$search}%")
                    ->orWhere('semester', 'like', "%{$search}%");
            })
            ->orderBy('is_active', 'desc')
            ->orderBy('tahun_ajaran', 'desc')
            ->paginate(10)
            ->appends(['search' => $search]);

        return view('walikelas.tahunajaran.index', compact('tahunAjaran', 'search'));
    }

    public function show(TahunAjaran $tahunajaran)
    {
        $tahunajaran->load('siswa', 'penilaian');
        return view('walikelas.tahunajaran.show', compact('tahunajaran'));
    }
}
