<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $kriteria = Kriteria::query()
            ->withCount('subKriteria')
            ->when($search, function ($query, $search) {
                return $query->where('kode_kriteria', 'like', "%{$search}%")
                    ->orWhere('nama_kriteria', 'like', "%{$search}%")
                    ->orWhere('jenis_kriteria', 'like', "%{$search}%");
            })
            ->orderBy('kode_kriteria')
            ->paginate(10)
            ->appends(['search' => $search]);

        return view('walikelas.kriteria.index', compact('kriteria', 'search'));
    }

    public function show(Kriteria $kriterium)
    {
        $kriterium->load('subKriteria', 'penilaian');
        return view('walikelas.kriteria.show', compact('kriterium'));
    }
}
