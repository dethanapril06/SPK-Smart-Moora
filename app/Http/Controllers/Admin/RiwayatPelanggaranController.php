<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RiwayatPelanggaran;
use App\Models\Siswa;
use App\Models\JenisPelanggaran;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use Illuminate\Http\Request;

class RiwayatPelanggaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $filterTA = $request->get('tahun_ajaran');
        $filterKategori = $request->get('kategori');
        $filterKelas = $request->get('kelas');
        $filterTanggalMulai = $request->get('tanggal_mulai');
        $filterTanggalSelesai = $request->get('tanggal_selesai');
        
        $riwayat = RiwayatPelanggaran::with(['siswa.kelas', 'jenisPelanggaran', 'tahunAjaran'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('siswa', function($q) use ($search) {
                    $q->where('nama_siswa', 'like', "%{$search}%")
                      ->orWhere('nisn', 'like', "%{$search}%");
                })
                ->orWhereHas('jenisPelanggaran', function($q) use ($search) {
                    $q->where('nama_pelanggaran', 'like', "%{$search}%");
                });
            })
            ->when($filterTA, function ($query, $filterTA) {
                return $query->where('id_ta', $filterTA);
            })
            ->when($filterKategori, function ($query, $filterKategori) {
                return $query->whereHas('jenisPelanggaran', function($q) use ($filterKategori) {
                    $q->where('kategori_pelanggaran', $filterKategori);
                });
            })
            ->when($filterKelas, function ($query, $filterKelas) {
                return $query->whereHas('siswa', function($q) use ($filterKelas) {
                    $q->where('id_kelas', $filterKelas);
                });
            })
            ->when($filterTanggalMulai, function ($query, $filterTanggalMulai) {
                return $query->whereDate('tanggal_kejadian', '>=', $filterTanggalMulai);
            })
            ->when($filterTanggalSelesai, function ($query, $filterTanggalSelesai) {
                return $query->whereDate('tanggal_kejadian', '<=', $filterTanggalSelesai);
            })
            ->orderBy('tanggal_kejadian', 'desc')
            ->paginate(10)
            ->appends([
                'search' => $search,
                'tahun_ajaran' => $filterTA,
                'kategori' => $filterKategori,
                'kelas' => $filterKelas,
                'tanggal_mulai' => $filterTanggalMulai,
                'tanggal_selesai' => $filterTanggalSelesai
            ]);
        
        // Data untuk filter dropdowns
        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();
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
        
        return view('admin.riwayatpelanggaran.index', compact(
            'riwayat', 
            'search', 
            'filterTA', 
            'filterKategori', 
            'filterKelas',
            'filterTanggalMulai',
            'filterTanggalSelesai',
            'tahunAjaranList', 
            'kelasList', 
            'kategoriList'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $siswaList = Siswa::with('kelas', 'tahunAjaran')->orderBy('nama_siswa')->get();
        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        $tahunAjaranAktif = TahunAjaran::where('is_active', 1)->first();
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
        
        return view('admin.riwayatpelanggaran.create', compact(
            'siswaList', 
            'tahunAjaranList', 
            'tahunAjaranAktif',
            'kategoriList'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_siswa' => 'required|exists:tb_siswa,id_siswa',
            'id_jenis_pelanggaran' => 'required|exists:tb_jenis_pelanggaran,id_jenis_pelanggaran',
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'tanggal_kejadian' => 'required|date',
            'keterangan_tambahan' => 'nullable|string'
        ], [
            'id_siswa.required' => 'Siswa wajib dipilih.',
            'id_siswa.exists' => 'Siswa tidak valid.',
            'id_jenis_pelanggaran.required' => 'Jenis pelanggaran wajib dipilih.',
            'id_jenis_pelanggaran.exists' => 'Jenis pelanggaran tidak valid.',
            'id_ta.required' => 'Tahun ajaran wajib dipilih.',
            'id_ta.exists' => 'Tahun ajaran tidak valid.',
            'tanggal_kejadian.required' => 'Tanggal kejadian wajib diisi.',
            'tanggal_kejadian.date' => 'Format tanggal tidak valid.'
        ]);

        RiwayatPelanggaran::create($validated);

        return redirect()->route('admin.riwayatpelanggaran.index')
            ->with('success', 'Riwayat pelanggaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RiwayatPelanggaran $riwayatpelanggaran)
    {
        $riwayatpelanggaran->load(['siswa.kelas', 'jenisPelanggaran', 'tahunAjaran']);
        
        // Get all violations for this student in the same academic year
        $riwayatSiswa = RiwayatPelanggaran::with('jenisPelanggaran')
            ->where('id_siswa', $riwayatpelanggaran->id_siswa)
            ->where('id_ta', $riwayatpelanggaran->id_ta)
            ->orderBy('tanggal_kejadian', 'desc')
            ->get();
        
        // Calculate total points
        $totalPoin = $riwayatSiswa->sum(function($item) {
            return $item->jenisPelanggaran->bobot_poin;
        });
        
        return view('admin.riwayatpelanggaran.show', compact('riwayatpelanggaran', 'riwayatSiswa', 'totalPoin'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RiwayatPelanggaran $riwayatpelanggaran)
    {
        $riwayatpelanggaran->load(['siswa', 'jenisPelanggaran']);
        $siswaList = Siswa::with('kelas', 'tahunAjaran')->orderBy('nama_siswa')->get();
        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
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
        
        return view('admin.riwayatpelanggaran.edit', compact(
            'riwayatpelanggaran',
            'siswaList', 
            'tahunAjaranList',
            'kategoriList'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RiwayatPelanggaran $riwayatpelanggaran)
    {
        $validated = $request->validate([
            'id_siswa' => 'required|exists:tb_siswa,id_siswa',
            'id_jenis_pelanggaran' => 'required|exists:tb_jenis_pelanggaran,id_jenis_pelanggaran',
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'tanggal_kejadian' => 'required|date',
            'keterangan_tambahan' => 'nullable|string'
        ], [
            'id_siswa.required' => 'Siswa wajib dipilih.',
            'id_siswa.exists' => 'Siswa tidak valid.',
            'id_jenis_pelanggaran.required' => 'Jenis pelanggaran wajib dipilih.',
            'id_jenis_pelanggaran.exists' => 'Jenis pelanggaran tidak valid.',
            'id_ta.required' => 'Tahun ajaran wajib dipilih.',
            'id_ta.exists' => 'Tahun ajaran tidak valid.',
            'tanggal_kejadian.required' => 'Tanggal kejadian wajib diisi.',
            'tanggal_kejadian.date' => 'Format tanggal tidak valid.'
        ]);

        $riwayatpelanggaran->update($validated);

        return redirect()->route('admin.riwayatpelanggaran.index')
            ->with('success', 'Riwayat pelanggaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RiwayatPelanggaran $riwayatpelanggaran)
    {
        $riwayatpelanggaran->delete();

        return redirect()->route('admin.riwayatpelanggaran.index')
            ->with('success', 'Riwayat pelanggaran berhasil dihapus.');
    }

    /**
     * Get jenis pelanggaran by kategori (AJAX)
     */
    public function getJenisPelanggaranByKategori(Request $request)
    {
        $kategori = $request->get('kategori');
        
        $jenisPelanggaran = JenisPelanggaran::where('kategori_pelanggaran', $kategori)
            ->orderBy('nama_pelanggaran')
            ->get();
        
        return response()->json($jenisPelanggaran);
    }
}
