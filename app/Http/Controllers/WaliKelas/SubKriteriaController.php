<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\SubKriteria;
use App\Models\Kriteria;
use Illuminate\Http\Request;

class SubKriteriaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $filterKriteria = $request->get('kriteria');

        $subkriteria = SubKriteria::with('kriteria')
            ->when($search, function ($query, $search) {
                return $query->where('nama_subkriteria', 'like', "%{$search}%")
                    ->orWhereHas('kriteria', function ($q) use ($search) {
                        $q->where('nama_kriteria', 'like', "%{$search}%")
                          ->orWhere('kode_kriteria', 'like', "%{$search}%");
                    });
            })
            ->when($filterKriteria, function ($query, $filterKriteria) {
                return $query->where('id_kriteria', $filterKriteria);
            })
            ->orderBy('id_kriteria')
            ->orderBy('bobot_subkriteria', 'desc')
            ->paginate(10)
            ->appends(['search' => $search, 'kriteria' => $filterKriteria]);

        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();

        return view('walikelas.subkriteria.index', compact('subkriteria', 'search', 'filterKriteria', 'kriteriaList'));
    }
}
