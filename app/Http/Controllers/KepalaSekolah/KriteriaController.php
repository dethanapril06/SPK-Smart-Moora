<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\SubKriteria;

class KriteriaController extends Controller
{
    public function index()
    {
        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();
        $subKriteriaList = SubKriteria::with('kriteria')->orderBy('id_kriteria')->orderBy('bobot_subkriteria', 'desc')->get();
        $totalBobot = $kriteriaList->sum('bobot');

        return view('kepalasekolah.kriteria.index', compact('kriteriaList', 'subKriteriaList', 'totalBobot'));
    }
}
