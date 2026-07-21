<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\HasilAkhir;
use App\Models\HasilFinalis;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Http\Request;

class PerhitunganController extends Controller
{
    // ─── SMART ──────────────────────────────────────────────────────────────────

    public function indexSmart(Request $request)
    {
        $filterTA         = $request->get('tahun_ajaran');
        $filterSemester   = $request->get('semester');
        $source           = $request->get('source', 'admin');
        $filterKelas      = $request->get('kelas');
        $tahunAjaranAktif = TahunAjaran::where('is_active', 1)->first();

        if (!$filterTA && $tahunAjaranAktif) {
            $filterTA = $tahunAjaranAktif->id_ta;
        }

        $hasilList    = HasilAkhir::whereRaw('1 = 0')->paginate(10);
        $sourceName   = 'Admin';
        $hasCalculation = false;

        if ($filterTA) {
            if ($source === 'admin') {
                $adminUser = User::where('level', 'Admin')->first();
                if ($adminUser) {
                    $hasilList = HasilAkhir::with(['siswa.kelas', 'tahunAjaran'])
                        ->where('id_ta', $filterTA)
                        ->when($filterSemester, fn($q, $s) => $q->where('id_semester', $s))
                        ->where('user_id', $adminUser->id)
                        ->whereNotNull('rank_smart')
                        ->when($filterKelas, fn($q) => $q->whereHas('siswa', fn($sq) =>
                            $sq->where('id_kelas', $filterKelas)
                        ))
                        ->orderBy('rank_smart')
                        ->paginate(10)
                        ->withQueryString();
                }
                $sourceName = 'Admin (Semua Siswa)';
            } else {
                $waliKelas = User::where('level', 'Wali Kelas')->find($source);
                if ($waliKelas) {
                    $kelas = Kelas::where('id_wali_kelas', $waliKelas->id)->first();
                    $hasilList = HasilAkhir::with(['siswa.kelas', 'tahunAjaran'])
                        ->where('id_ta', $filterTA)
                        ->when($filterSemester, fn($q, $s) => $q->where('id_semester', $s))
                        ->where('user_id', $waliKelas->id)
                        ->whereNotNull('rank_smart')
                        ->orderBy('rank_smart')
                        ->paginate(10)
                        ->withQueryString();
                    $sourceName = 'Wali Kelas: ' . $waliKelas->name
                        . ($kelas ? ' (' . $kelas->nama_kelas . ')' : '');
                }
            }

            $hasCalculation = $hasilList->total() > 0;
        }

        $tahunAjaranList = TahunAjaran::representatives()->orderBy('tahun_ajaran', 'desc')->get();
        $semesterList    = Semester::orderBy('id_semester')->get();
        $kelasList       = Kelas::orderBy('nama_kelas')->get();
        $sourceList      = $this->getSourceList($filterTA, $filterSemester, 'smart');

        return view('kepalasekolah.perhitungan.smart.index', compact(
            'hasilList', 'tahunAjaranList', 'semesterList', 'kelasList',
            'filterTA', 'filterSemester', 'filterKelas', 'source', 'sourceName',
            'hasCalculation', 'sourceList'
        ));
    }

    // ─── MOORA ──────────────────────────────────────────────────────────────────

    public function indexMoora(Request $request)
    {
        $filterTA         = $request->get('tahun_ajaran');
        $filterSemester   = $request->get('semester');
        $source           = $request->get('source', 'admin');
        $filterKelas      = $request->get('kelas');
        $tahunAjaranAktif = TahunAjaran::where('is_active', 1)->first();

        if (!$filterTA && $tahunAjaranAktif) {
            $filterTA = $tahunAjaranAktif->id_ta;
        }

        $hasilList    = HasilAkhir::whereRaw('1 = 0')->paginate(10);
        $sourceName   = 'Admin';
        $hasCalculation = false;

        if ($filterTA) {
            if ($source === 'admin') {
                $adminUser = User::where('level', 'Admin')->first();
                if ($adminUser) {
                    $hasilList = HasilAkhir::with(['siswa.kelas', 'tahunAjaran'])
                        ->where('id_ta', $filterTA)
                        ->when($filterSemester, fn($q, $s) => $q->where('id_semester', $s))
                        ->where('user_id', $adminUser->id)
                        ->whereNotNull('rank_moora')
                        ->when($filterKelas, fn($q) => $q->whereHas('siswa', fn($sq) =>
                            $sq->where('id_kelas', $filterKelas)
                        ))
                        ->orderBy('rank_moora')
                        ->paginate(10)
                        ->withQueryString();
                }
                $sourceName = 'Admin (Semua Siswa)';
            } else {
                $waliKelas = User::where('level', 'Wali Kelas')->find($source);
                if ($waliKelas) {
                    $kelas = Kelas::where('id_wali_kelas', $waliKelas->id)->first();
                    $hasilList = HasilAkhir::with(['siswa.kelas', 'tahunAjaran'])
                        ->where('id_ta', $filterTA)
                        ->when($filterSemester, fn($q, $s) => $q->where('id_semester', $s))
                        ->where('user_id', $waliKelas->id)
                        ->whereNotNull('rank_moora')
                        ->orderBy('rank_moora')
                        ->paginate(10)
                        ->withQueryString();
                    $sourceName = 'Wali Kelas: ' . $waliKelas->name
                        . ($kelas ? ' (' . $kelas->nama_kelas . ')' : '');
                }
            }

            $hasCalculation = $hasilList->total() > 0;
        }

        $tahunAjaranList = TahunAjaran::representatives()->orderBy('tahun_ajaran', 'desc')->get();
        $semesterList    = Semester::orderBy('id_semester')->get();
        $kelasList       = Kelas::orderBy('nama_kelas')->get();
        $sourceList      = $this->getSourceList($filterTA, $filterSemester, 'moora');

        return view('kepalasekolah.perhitungan.moora.index', compact(
            'hasilList', 'tahunAjaranList', 'semesterList', 'kelasList',
            'filterTA', 'filterSemester', 'filterKelas', 'source', 'sourceName',
            'hasCalculation', 'sourceList'
        ));
    }

    // ─── COMPARE ────────────────────────────────────────────────────────────────

    public function indexFinalisSmart(Request $request)
    {
        return $this->indexFinalis($request, 'smart');
    }

    public function indexFinalisMoora(Request $request)
    {
        return $this->indexFinalis($request, 'moora');
    }

    protected function indexFinalis(Request $request, string $method)
    {
        $filterTA = $request->get('tahun_ajaran');
        $filterSemester = $request->get('semester');
        $tahunAjaranAktif = TahunAjaran::where('is_active', 1)->first();
        $adminUser = User::where('level', 'Admin')->first();

        if (!$filterTA && $tahunAjaranAktif) {
            $filterTA = $tahunAjaranAktif->id_ta;
        }

        $hasilByTingkat = HasilFinalis::with(['siswa.kelas', 'tahunAjaran'])
            ->when($adminUser, fn($query) => $query->where('user_id', $adminUser->id))
            ->when(!$adminUser, fn($query) => $query->whereRaw('1 = 0'))
            ->where('metode', $method)
            ->when($filterTA, fn($query) => $query->where('id_ta', $filterTA))
            ->when($filterSemester, fn($query, $s) => $query->where('id_semester', $s))
            ->orderByRaw("FIELD(tingkat, 'X', 'XI', 'XII')")
            ->orderBy('rank')
            ->get()
            ->groupBy('tingkat');

        $tahunAjaranList = TahunAjaran::representatives()->orderBy('tahun_ajaran', 'desc')->get();
        $semesterList    = Semester::orderBy('id_semester')->get();
        $hasCalculation = $filterTA && $hasilByTingkat->flatten(1)->isNotEmpty();

        return view('kepalasekolah.perhitungan.finalis.index', compact(
            'hasilByTingkat',
            'tahunAjaranList',
            'semesterList',
            'filterTA',
            'filterSemester',
            'hasCalculation',
            'method'
        ));
    }

    public function compare($id_ta, Request $request)
    {
        $source         = $request->get('source', 'admin');
        $filterSemester = $request->get('semester');
        $tahunAjaran    = TahunAjaran::findOrFail($id_ta);
        $userId         = $this->resolveUserId($source);

        $hasilList = HasilAkhir::with('siswa')
            ->where('id_ta', $id_ta)
            ->when($filterSemester, fn($q, $s) => $q->where('id_semester', $s))
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->get();

        $agreement = $hasilList->filter(fn($h) => $h->rank_smart == $h->rank_moora)->count();

        $agreementPercentage = $hasilList->count() > 0
            ? round(($agreement / $hasilList->count()) * 100, 2)
            : 0;

        $sourceName   = $this->resolveSourceName($source);
        $semesterList = Semester::orderBy('id_semester')->get();

        return view('kepalasekolah.perhitungan.compare', compact(
            'tahunAjaran', 'hasilList', 'agreementPercentage', 'source', 'sourceName', 'semesterList', 'filterSemester'
        ));
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────

    /**
     * @param string|null $filterTA
     * @param string|null $filterSemester
     * @param string      $method  'smart' | 'moora'
     */
    protected function getSourceList($filterTA, $filterSemester, string $method = 'smart'): array
    {
        $sources  = [];
        $rankCol  = "rank_{$method}";   // rank_smart atau rank_moora

        if (!$filterTA) return $sources;

        $adminUser = User::where('level', 'Admin')->first();
        if ($adminUser) {
            $exists = HasilAkhir::where('id_ta', $filterTA)
                ->when($filterSemester, fn($q, $s) => $q->where('id_semester', $s))
                ->where('user_id', $adminUser->id)
                ->whereNotNull($rankCol)
                ->exists();
            if ($exists) {
                $sources[] = ['value' => 'admin', 'label' => 'Admin (Semua Siswa)'];
            }
        }

        foreach (User::where('level', 'Wali Kelas')->get() as $wk) {
            $exists = HasilAkhir::where('id_ta', $filterTA)
                ->when($filterSemester, fn($q, $s) => $q->where('id_semester', $s))
                ->where('user_id', $wk->id)
                ->whereNotNull($rankCol)
                ->exists();
            if ($exists) {
                $kelas     = Kelas::where('id_wali_kelas', $wk->id)->first();
                $sources[] = [
                    'value' => $wk->id,
                    'label' => $wk->name . ($kelas ? ' (' . $kelas->nama_kelas . ')' : ''),
                ];
            }
        }

        return $sources;
    }

    protected function resolveUserId($source): ?int
    {
        if ($source === 'admin') {
            $admin = User::where('level', 'Admin')->first();
            return $admin?->id;
        }
        return (int) $source;
    }

    protected function resolveSourceName($source): string
    {
        if ($source === 'admin') return 'Admin (Semua Siswa)';

        $user = User::find($source);
        if ($user) {
            $kelas = Kelas::where('id_wali_kelas', $user->id)->first();
            return 'Wali Kelas: ' . $user->name . ($kelas ? ' (' . $kelas->nama_kelas . ')' : '');
        }
        return 'Tidak Diketahui';
    }
}
