<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $mataPelajaran = MataPelajaran::query()
            ->when($search, function ($query, $search) {
                return $query->where('kode_mapel', 'like', "%{$search}%")
                    ->orWhere('nama_mapel', 'like', "%{$search}%");
            })
            ->orderBy('kode_mapel')
            ->paginate(10)
            ->appends(['search' => $search]);

        return view('kepalasekolah.matapelajaran.index', compact('mataPelajaran', 'search'));
    }
}
