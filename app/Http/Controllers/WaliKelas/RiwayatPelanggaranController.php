<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\RiwayatPelanggaran;
use App\Models\Siswa;
use App\Models\JenisPelanggaran;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\Kelas;
use Illuminate\Http\Request;

class RiwayatPelanggaranController extends Controller
{
    protected function getKelas()
    {
        $kelas = Kelas::where('id_wali_kelas', auth()->id())->first();
        abort_if(!$kelas, 403, 'Anda belum ditugaskan sebagai wali kelas.');
        return $kelas;
    }

    protected function getSiswaIds()
    {
        $kelas = $this->getKelas();
        return Siswa::where('id_kelas', $kelas->id_kelas)->pluck('id_siswa');
    }

    public function index(Request $request)
    {
        $kelas = $this->getKelas();
        $siswaIds = Siswa::where('id_kelas', $kelas->id_kelas)->pluck('id_siswa');

        $search = $request->get('search');
        $filterTA = $request->get('tahun_ajaran');
        $filterSemester = $request->get('semester');
        $filterKategori = $request->get('kategori');
        $filterTanggalMulai = $request->get('tanggal_mulai');
        $filterTanggalSelesai = $request->get('tanggal_selesai');

        $riwayat = RiwayatPelanggaran::with(['siswa.kelas', 'jenisPelanggaran', 'tahunAjaran', 'semester'])
            ->whereIn('id_siswa', $siswaIds)
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->whereHas('siswa', function ($sq) use ($search) {
                        $sq->where('nama_siswa', 'like', "%{$search}%")
                          ->orWhere('nisn', 'like', "%{$search}%");
                    })
                    ->orWhereHas('jenisPelanggaran', function ($sq) use ($search) {
                        $sq->where('nama_pelanggaran', 'like', "%{$search}%");
                    });
                });
            })
            ->when($filterTA, function ($query, $filterTA) {
                return $query->where('id_ta', $filterTA);
            })
            ->when($filterSemester, function ($query, $filterSemester) {
                return $query->where('id_semester', $filterSemester);
            })
            ->when($filterKategori, function ($query, $filterKategori) {
                return $query->whereHas('jenisPelanggaran', function ($q) use ($filterKategori) {
                    $q->where('kategori_pelanggaran', $filterKategori);
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
                'semester' => $filterSemester,
                'kategori' => $filterKategori,
                'tanggal_mulai' => $filterTanggalMulai,
                'tanggal_selesai' => $filterTanggalSelesai
            ]);

        $tahunAjaranList = TahunAjaran::representatives()->orderBy('tahun_ajaran', 'desc')->get();
        $semesterList = Semester::orderBy('id_semester')->get();
        $kategoriList = [
            'Keterlambatan', 'Kehadiran', 'Pakaian', 'Kelakuan',
            'Ketertiban', 'Kerajinan', 'Narkoba_Miras', 'Tata_Tertib_Ujian'
        ];

        return view('walikelas.riwayatpelanggaran.index', compact(
            'riwayat', 'search', 'filterTA', 'filterSemester', 'filterKategori',
            'filterTanggalMulai', 'filterTanggalSelesai',
            'tahunAjaranList', 'semesterList', 'kategoriList', 'kelas'
        ));
    }

    public function create()
    {
        $kelas = $this->getKelas();
        $siswaList = Siswa::with('kelas', 'tahunAjaran')
            ->where('id_kelas', $kelas->id_kelas)
            ->orderBy('nama_siswa')
            ->get();
        $tahunAjaranList = TahunAjaran::representatives()->orderBy('tahun_ajaran', 'desc')->get();
        $semesterList = Semester::orderBy('id_semester')->get();
        $tahunAjaranAktif = TahunAjaran::where('is_active', 1)->first();
        $kategoriList = [
            'Keterlambatan', 'Kehadiran', 'Pakaian', 'Kelakuan',
            'Ketertiban', 'Kerajinan', 'Narkoba_Miras', 'Tata_Tertib_Ujian'
        ];

        return view('walikelas.riwayatpelanggaran.create', compact(
            'siswaList', 'tahunAjaranList', 'semesterList', 'tahunAjaranAktif', 'kategoriList'
        ));
    }

    public function store(Request $request)
    {
        $kelas = $this->getKelas();

        $validated = $request->validate([
            'id_siswa' => 'required|exists:tb_siswa,id_siswa',
            'id_jenis_pelanggaran' => 'required|exists:tb_jenis_pelanggaran,id_jenis_pelanggaran',
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'id_semester' => 'required|exists:tb_semester,id_semester',
            'tanggal_kejadian' => 'required|date',
            'keterangan_tambahan' => 'nullable|string'
        ], [
            'id_siswa.required' => 'Siswa wajib dipilih.',
            'id_jenis_pelanggaran.required' => 'Jenis pelanggaran wajib dipilih.',
            'id_ta.required' => 'Tahun ajaran wajib dipilih.',
            'id_semester.required' => 'Semester wajib dipilih.',
            'tanggal_kejadian.required' => 'Tanggal kejadian wajib diisi.',
        ]);

        // Pastikan siswa milik kelas wali kelas
        $siswa = Siswa::findOrFail($validated['id_siswa']);
        abort_if($siswa->id_kelas !== $kelas->id_kelas, 403, 'Siswa bukan anggota kelas Anda.');

        RiwayatPelanggaran::create($validated);

        return redirect()->route('walikelas.riwayatpelanggaran.index')
            ->with('success', 'Riwayat pelanggaran berhasil ditambahkan.');
    }

    public function show(RiwayatPelanggaran $riwayatpelanggaran)
    {
        $kelas = $this->getKelas();
        abort_if($riwayatpelanggaran->siswa->id_kelas !== $kelas->id_kelas, 403, 'Data bukan milik kelas Anda.');

        $riwayatpelanggaran->load(['siswa.kelas', 'jenisPelanggaran', 'tahunAjaran', 'semester']);

        $riwayatSiswa = RiwayatPelanggaran::with('jenisPelanggaran')
            ->where('id_siswa', $riwayatpelanggaran->id_siswa)
            ->where('id_ta', $riwayatpelanggaran->id_ta)
            ->where('id_semester', $riwayatpelanggaran->id_semester)
            ->orderBy('tanggal_kejadian', 'desc')
            ->get();

        $totalPoin = $riwayatSiswa->sum(function ($item) {
            return $item->jenisPelanggaran->bobot_poin;
        });

        return view('walikelas.riwayatpelanggaran.show', compact('riwayatpelanggaran', 'riwayatSiswa', 'totalPoin'));
    }

    public function edit(RiwayatPelanggaran $riwayatpelanggaran)
    {
        $kelas = $this->getKelas();
        abort_if($riwayatpelanggaran->siswa->id_kelas !== $kelas->id_kelas, 403, 'Data bukan milik kelas Anda.');

        $riwayatpelanggaran->load(['siswa', 'jenisPelanggaran']);
        $siswaList = Siswa::with('kelas', 'tahunAjaran')
            ->where('id_kelas', $kelas->id_kelas)
            ->orderBy('nama_siswa')
            ->get();
        $tahunAjaranList = TahunAjaran::representatives()->orderBy('tahun_ajaran', 'desc')->get();
        $semesterList = Semester::orderBy('id_semester')->get();
        $kategoriList = [
            'Keterlambatan', 'Kehadiran', 'Pakaian', 'Kelakuan',
            'Ketertiban', 'Kerajinan', 'Narkoba_Miras', 'Tata_Tertib_Ujian'
        ];

        return view('walikelas.riwayatpelanggaran.edit', compact(
            'riwayatpelanggaran', 'siswaList', 'tahunAjaranList', 'semesterList', 'kategoriList'
        ));
    }

    public function update(Request $request, RiwayatPelanggaran $riwayatpelanggaran)
    {
        $kelas = $this->getKelas();
        abort_if($riwayatpelanggaran->siswa->id_kelas !== $kelas->id_kelas, 403, 'Data bukan milik kelas Anda.');

        $validated = $request->validate([
            'id_siswa' => 'required|exists:tb_siswa,id_siswa',
            'id_jenis_pelanggaran' => 'required|exists:tb_jenis_pelanggaran,id_jenis_pelanggaran',
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'id_semester' => 'required|exists:tb_semester,id_semester',
            'tanggal_kejadian' => 'required|date',
            'keterangan_tambahan' => 'nullable|string'
        ]);

        $siswa = Siswa::findOrFail($validated['id_siswa']);
        abort_if($siswa->id_kelas !== $kelas->id_kelas, 403, 'Siswa bukan anggota kelas Anda.');

        $riwayatpelanggaran->update($validated);

        return redirect()->route('walikelas.riwayatpelanggaran.index')
            ->with('success', 'Riwayat pelanggaran berhasil diperbarui.');
    }

    public function destroy(RiwayatPelanggaran $riwayatpelanggaran)
    {
        $kelas = $this->getKelas();
        abort_if($riwayatpelanggaran->siswa->id_kelas !== $kelas->id_kelas, 403, 'Data bukan milik kelas Anda.');

        $riwayatpelanggaran->delete();

        return redirect()->route('walikelas.riwayatpelanggaran.index')
            ->with('success', 'Riwayat pelanggaran berhasil dihapus.');
    }

    public function getJenisPelanggaranByKategori(Request $request)
    {
        $kategori = $request->get('kategori');
        $jenisPelanggaran = JenisPelanggaran::where('kategori_pelanggaran', $kategori)
            ->orderBy('nama_pelanggaran')
            ->get();

        return response()->json($jenisPelanggaran);
    }
}
