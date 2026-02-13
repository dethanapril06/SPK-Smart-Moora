<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\JenisPelanggaran;
use Illuminate\Http\Request;

class JenisPelanggaranController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $filterKategori = $request->get('kategori');

        $jenispelanggaran = JenisPelanggaran::query()
            ->when($search, function ($query, $search) {
                return $query->where('nama_pelanggaran', 'like', "%{$search}%")
                    ->orWhere('kategori_pelanggaran', 'like', "%{$search}%");
            })
            ->when($filterKategori, function ($query, $filterKategori) {
                return $query->where('kategori_pelanggaran', $filterKategori);
            })
            ->orderBy('kategori_pelanggaran')
            ->orderBy('bobot_poin', 'desc')
            ->paginate(10)
            ->appends(['search' => $search, 'kategori' => $filterKategori]);

        $kategoriList = [
            'Keterlambatan',
            'Kehadiran',
            'Pakaian',
            'Kelakuan',
            'Ketertiban',
            'Kerajinan',
            'Narkoba_Miras',
            'Tata_Tertib_Ujian'
        ];

        return view('walikelas.jenispelanggaran.index', compact('jenispelanggaran', 'search', 'filterKategori', 'kategoriList'));
    }
}
