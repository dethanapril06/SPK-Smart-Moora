<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Exports\HasilPerangkinganExport;
use App\Models\HasilAkhir;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function exportPdf(Request $request)
    {
        $filterTA = $request->get('tahun_ajaran');
        $source = $request->get('source', 'admin');
        $filterKelas = $request->get('kelas');

        $tahunAjaran = TahunAjaran::findOrFail($filterTA);
        $userId = $this->resolveUserId($source);
        $sourceName = $this->resolveSourceName($source);

        $hasilList = HasilAkhir::with(['siswa.kelas', 'tahunAjaran'])
            ->where('id_ta', $filterTA)
            ->when($userId, function ($q, $userId) {
                $q->where('user_id', $userId);
            })
            ->when($filterKelas, function ($q, $filterKelas) {
                $q->whereHas('siswa', function ($sq) use ($filterKelas) {
                    $sq->where('id_kelas', $filterKelas);
                });
            })
            ->get()
            ->sortBy('rank_smart')
            ->values();

        $pdf = Pdf::loadView('kepalasekolah.report.pdf', compact(
            'hasilList',
            'tahunAjaran',
            'sourceName',
            'filterKelas'
        ))->setPaper('a4', 'landscape');

        $filename = 'Laporan_Perangkingan_' . str_replace(['/', '\\', ' '], ['_', '_', '_'], $tahunAjaran->tahun_ajaran) . '_' . $tahunAjaran->semester . '.pdf';

        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        $filterTA = $request->get('tahun_ajaran');
        $source = $request->get('source', 'admin');
        $filterKelas = $request->get('kelas');

        $tahunAjaran = TahunAjaran::findOrFail($filterTA);
        $userId = $this->resolveUserId($source);
        $sourceName = $this->resolveSourceName($source);

        $filename = 'Laporan_Perangkingan_' . str_replace(['/', '\\', ' '], ['_', '_', '_'], $tahunAjaran->tahun_ajaran) . '_' . $tahunAjaran->semester . '.xlsx';

        return Excel::download(
            new HasilPerangkinganExport($filterTA, $userId, $filterKelas, $tahunAjaran, $sourceName),
            $filename
        );
    }

    protected function resolveUserId($source)
    {
        if ($source === 'admin') {
            $admin = User::where('level', 'Admin')->first();
            return $admin ? $admin->id : null;
        }
        return (int) $source;
    }

    protected function resolveSourceName($source)
    {
        if ($source === 'admin') {
            return 'Admin (Semua Siswa)';
        }
        $user = User::find($source);
        if ($user) {
            $kelas = Kelas::where('id_wali_kelas', $user->id)->first();
            return 'Wali Kelas: ' . $user->name . ($kelas ? ' (' . $kelas->nama_kelas . ')' : '');
        }
        return 'Tidak Diketahui';
    }
}
