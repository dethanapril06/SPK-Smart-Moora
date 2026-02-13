<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisPelanggaran;
use Illuminate\Http\Request;

class JenisPelanggaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
        
        // List of categories from migration
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
        
        return view('admin.jenispelanggaran.index', compact('jenispelanggaran', 'search', 'filterKategori', 'kategoriList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
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
        
        return view('admin.jenispelanggaran.create', compact('kategoriList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_pelanggaran' => 'required|in:Keterlambatan,Kehadiran,Pakaian,Kelakuan,Ketertiban,Kerajinan,Narkoba_Miras,Tata_Tertib_Ujian',
            'nama_pelanggaran' => 'required|string',
            'bobot_poin' => 'required|integer|min:0'
        ], [
            'kategori_pelanggaran.required' => 'Kategori pelanggaran wajib dipilih.',
            'kategori_pelanggaran.in' => 'Kategori pelanggaran tidak valid.',
            'nama_pelanggaran.required' => 'Nama pelanggaran wajib diisi.',
            'bobot_poin.required' => 'Bobot poin wajib diisi.',
            'bobot_poin.integer' => 'Bobot poin harus berupa angka.',
            'bobot_poin.min' => 'Bobot poin minimal 0.'
        ]);

        JenisPelanggaran::create($validated);

        return redirect()->route('admin.jenispelanggaran.index')
            ->with('success', 'Jenis pelanggaran berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JenisPelanggaran $jenispelanggaran)
    {
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
        
        return view('admin.jenispelanggaran.edit', compact('jenispelanggaran', 'kategoriList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JenisPelanggaran $jenispelanggaran)
    {
        $validated = $request->validate([
            'kategori_pelanggaran' => 'required|in:Keterlambatan,Kehadiran,Pakaian,Kelakuan,Ketertiban,Kerajinan,Narkoba_Miras,Tata_Tertib_Ujian',
            'nama_pelanggaran' => 'required|string',
            'bobot_poin' => 'required|integer|min:0'
        ], [
            'kategori_pelanggaran.required' => 'Kategori pelanggaran wajib dipilih.',
            'kategori_pelanggaran.in' => 'Kategori pelanggaran tidak valid.',
            'nama_pelanggaran.required' => 'Nama pelanggaran wajib diisi.',
            'bobot_poin.required' => 'Bobot poin wajib diisi.',
            'bobot_poin.integer' => 'Bobot poin harus berupa angka.',
            'bobot_poin.min' => 'Bobot poin minimal 0.'
        ]);

        $jenispelanggaran->update($validated);

        return redirect()->route('admin.jenispelanggaran.index')
            ->with('success', 'Jenis pelanggaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JenisPelanggaran $jenispelanggaran)
    {
        $jenispelanggaran->delete();

        return redirect()->route('admin.jenispelanggaran.index')
            ->with('success', 'Jenis pelanggaran berhasil dihapus.');
    }
}
