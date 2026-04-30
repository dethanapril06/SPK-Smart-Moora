<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest Routes - Login Page
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Auth Routes - Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Profil Routes (shared across all roles)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profil', [\App\Http\Controllers\ProfilController::class, 'index'])->name('profil.index');
    Route::put('/profil', [\App\Http\Controllers\ProfilController::class, 'update'])->name('profil.update');
    Route::put('/profil/password', [\App\Http\Controllers\ProfilController::class, 'updatePassword'])->name('profil.password');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'check.level:Admin'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
        ->name('admin.dashboard');

    // Pengguna Management Routes
    Route::resource('pengguna', \App\Http\Controllers\Admin\PenggunaController::class, [
        'as' => 'admin'
    ]);
    Route::put('pengguna/{pengguna}/reset-password', [\App\Http\Controllers\Admin\PenggunaController::class, 'resetPassword'])
        ->name('admin.pengguna.reset-password');

    // Kelas Management Routes
    Route::resource('kelas', \App\Http\Controllers\Admin\KelasController::class, [
        'as' => 'admin'
    ]);

    // Tahun Ajaran Management Routes
    Route::resource('tahunajaran', \App\Http\Controllers\Admin\TahunAjaranController::class, [
        'as' => 'admin'
    ]);
    Route::put('tahunajaran/{tahunajaran}/set-active', [\App\Http\Controllers\Admin\TahunAjaranController::class, 'setActive'])
        ->name('admin.tahunajaran.set-active');

    // Siswa Management Routes
    Route::resource('siswa', \App\Http\Controllers\Admin\SiswaController::class, [
        'as' => 'admin'
    ]);

    // Kriteria Management Routes
    Route::resource('kriteria', \App\Http\Controllers\Admin\KriteriaController::class, [
        'as' => 'admin'
    ]);

    // Sub Kriteria Management Routes
    Route::resource('subkriteria', \App\Http\Controllers\Admin\SubKriteriaController::class, [
        'as' => 'admin'
    ]);

    // Jenis Pelanggaran Management Routes
    Route::resource('jenispelanggaran', \App\Http\Controllers\Admin\JenisPelanggaranController::class, [
        'as' => 'admin'
    ]);

    // Riwayat Pelanggaran Management Routes
    Route::get('riwayatpelanggaran/get-jenis-pelanggaran', [\App\Http\Controllers\Admin\RiwayatPelanggaranController::class, 'getJenisPelanggaranByKategori'])
        ->name('admin.riwayatpelanggaran.getJenisPelanggaran');
    Route::resource('riwayatpelanggaran', \App\Http\Controllers\Admin\RiwayatPelanggaranController::class, [
        'as' => 'admin'
    ]);

    // Penilaian Siswa Management Routes
    Route::get('penilaian/get-c5', [\App\Http\Controllers\Admin\PenilaianController::class, 'getC5'])
        ->name('admin.penilaian.getC5');
    Route::post('penilaian/aggregate', [\App\Http\Controllers\Admin\PenilaianController::class, 'aggregate'])
        ->name('admin.penilaian.aggregate');
    Route::get('penilaian', [\App\Http\Controllers\Admin\PenilaianController::class, 'index'])
        ->name('admin.penilaian.index');
    Route::get('penilaian/{penilaian}', [\App\Http\Controllers\Admin\PenilaianController::class, 'show'])
        ->name('admin.penilaian.show');

    // Perhitungan SMART (standalone)
    Route::prefix('perhitungan/smart')->name('admin.perhitungan.smart.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PerhitunganController::class, 'indexSmart'])->name('index');
        Route::post('/calculate', [\App\Http\Controllers\Admin\PerhitunganController::class, 'calculateSmart'])->name('calculate');
        Route::get('/steps/{id_ta}', [\App\Http\Controllers\Admin\PerhitunganController::class, 'showStepsSmart'])->name('steps');
    });

    // Perhitungan MOORA (standalone)
    Route::prefix('perhitungan/moora')->name('admin.perhitungan.moora.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PerhitunganController::class, 'indexMoora'])->name('index');
        Route::post('/calculate', [\App\Http\Controllers\Admin\PerhitunganController::class, 'calculateMoora'])->name('calculate');
        Route::get('/steps/{id_ta}', [\App\Http\Controllers\Admin\PerhitunganController::class, 'showStepsMoora'])->name('steps');
    });

    // --- Raw Score Input Routes ---

    // Mata Pelajaran Management
    Route::resource('matapelajaran', \App\Http\Controllers\Admin\MataPelajaranController::class, [
        'as' => 'admin'
    ]);

    // Nilai Pengetahuan (C1)
    Route::get('nilaipengetahuan', [\App\Http\Controllers\Admin\NilaiPengetahuanController::class, 'index'])
        ->name('admin.nilaipengetahuan.index');
    Route::post('nilaipengetahuan', [\App\Http\Controllers\Admin\NilaiPengetahuanController::class, 'store'])
        ->name('admin.nilaipengetahuan.store');

    // Nilai Keterampilan (C2)
    Route::get('nilaiketerampilan', [\App\Http\Controllers\Admin\NilaiKeterampilanController::class, 'index'])
        ->name('admin.nilaiketerampilan.index');
    Route::post('nilaiketerampilan', [\App\Http\Controllers\Admin\NilaiKeterampilanController::class, 'store'])
        ->name('admin.nilaiketerampilan.store');

    // Nilai Sikap (C3)
    Route::get('nilaisikap', [\App\Http\Controllers\Admin\NilaiSikapController::class, 'index'])
        ->name('admin.nilaisikap.index');
    Route::post('nilaisikap', [\App\Http\Controllers\Admin\NilaiSikapController::class, 'store'])
        ->name('admin.nilaisikap.store');

    // Nilai Ekstrakurikuler (C4)
    Route::get('nilaiekstrakurikuler', [\App\Http\Controllers\Admin\NilaiEkstrakurikulerController::class, 'index'])
        ->name('admin.nilaiekstrakurikuler.index');
    Route::get('nilaiekstrakurikuler/create', [\App\Http\Controllers\Admin\NilaiEkstrakurikulerController::class, 'create'])
        ->name('admin.nilaiekstrakurikuler.create');
    Route::post('nilaiekstrakurikuler', [\App\Http\Controllers\Admin\NilaiEkstrakurikulerController::class, 'store'])
        ->name('admin.nilaiekstrakurikuler.store');
    Route::get('nilaiekstrakurikuler/get-ekskul', [\App\Http\Controllers\Admin\NilaiEkstrakurikulerController::class, 'getEkskul'])
        ->name('admin.nilaiekstrakurikuler.getEkskul');
    Route::delete('nilaiekstrakurikuler/{id}', [\App\Http\Controllers\Admin\NilaiEkstrakurikulerController::class, 'destroy'])
        ->name('admin.nilaiekstrakurikuler.destroy');

    // Nilai Absensi (C6)
    Route::get('nilaiabsensi', [\App\Http\Controllers\Admin\NilaiAbsensiController::class, 'index'])
        ->name('admin.nilaiabsensi.index');
    Route::post('nilaiabsensi', [\App\Http\Controllers\Admin\NilaiAbsensiController::class, 'store'])
        ->name('admin.nilaiabsensi.store');

    // Tambahkan route admin lainnya di sini
});

/*
|--------------------------------------------------------------------------
| Wali Kelas Routes
|--------------------------------------------------------------------------
*/
Route::prefix('wali-kelas')->middleware(['auth', 'check.level:Wali Kelas'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\WaliKelas\DashboardController::class, 'index'])
        ->name('walikelas.dashboard');

    // Kelas (read-only)
    Route::get('kelas', [\App\Http\Controllers\WaliKelas\KelasController::class, 'index'])
        ->name('walikelas.kelas.index');
    Route::get('kelas/{kelas}', [\App\Http\Controllers\WaliKelas\KelasController::class, 'show'])
        ->name('walikelas.kelas.show');

    // Tahun Ajaran (read-only)
    Route::get('tahunajaran', [\App\Http\Controllers\WaliKelas\TahunAjaranController::class, 'index'])
        ->name('walikelas.tahunajaran.index');
    Route::get('tahunajaran/{tahunajaran}', [\App\Http\Controllers\WaliKelas\TahunAjaranController::class, 'show'])
        ->name('walikelas.tahunajaran.show');

    // Kriteria (read-only)
    Route::get('kriteria', [\App\Http\Controllers\WaliKelas\KriteriaController::class, 'index'])
        ->name('walikelas.kriteria.index');
    Route::get('kriteria/{kriteria}', [\App\Http\Controllers\WaliKelas\KriteriaController::class, 'show'])
        ->name('walikelas.kriteria.show');

    // Sub Kriteria (read-only)
    Route::get('subkriteria', [\App\Http\Controllers\WaliKelas\SubKriteriaController::class, 'index'])
        ->name('walikelas.subkriteria.index');

    // Jenis Pelanggaran (read-only)
    Route::get('jenispelanggaran', [\App\Http\Controllers\WaliKelas\JenisPelanggaranController::class, 'index'])
        ->name('walikelas.jenispelanggaran.index');

    // Siswa Management (scoped to wali kelas's class)
    Route::resource('siswa', \App\Http\Controllers\WaliKelas\SiswaController::class, [
        'as' => 'walikelas'
    ]);

    // Riwayat Pelanggaran Management (scoped to wali kelas's class)
    Route::get('riwayatpelanggaran/get-jenis-pelanggaran', [\App\Http\Controllers\WaliKelas\RiwayatPelanggaranController::class, 'getJenisPelanggaranByKategori'])
        ->name('walikelas.riwayatpelanggaran.getJenisPelanggaran');
    Route::resource('riwayatpelanggaran', \App\Http\Controllers\WaliKelas\RiwayatPelanggaranController::class, [
        'as' => 'walikelas'
    ]);

    // Penilaian Siswa Management (scoped to wali kelas's class)
    Route::get('penilaian/get-c5', [\App\Http\Controllers\WaliKelas\PenilaianController::class, 'getC5'])
        ->name('walikelas.penilaian.getC5');
    Route::post('penilaian/aggregate', [\App\Http\Controllers\WaliKelas\PenilaianController::class, 'aggregate'])
        ->name('walikelas.penilaian.aggregate');
    Route::get('penilaian', [\App\Http\Controllers\WaliKelas\PenilaianController::class, 'index'])
        ->name('walikelas.penilaian.index');
    Route::get('penilaian/{penilaian}', [\App\Http\Controllers\WaliKelas\PenilaianController::class, 'show'])
        ->name('walikelas.penilaian.show');

    // Nilai Pengetahuan (C1) - scoped to wali kelas's class
    Route::get('nilaipengetahuan', [\App\Http\Controllers\WaliKelas\NilaiPengetahuanController::class, 'index'])
        ->name('walikelas.nilaipengetahuan.index');
    Route::post('nilaipengetahuan', [\App\Http\Controllers\WaliKelas\NilaiPengetahuanController::class, 'store'])
        ->name('walikelas.nilaipengetahuan.store');

    // Nilai Keterampilan (C2) - scoped to wali kelas's class
    Route::get('nilaiketerampilan', [\App\Http\Controllers\WaliKelas\NilaiKeterampilanController::class, 'index'])
        ->name('walikelas.nilaiketerampilan.index');
    Route::post('nilaiketerampilan', [\App\Http\Controllers\WaliKelas\NilaiKeterampilanController::class, 'store'])
        ->name('walikelas.nilaiketerampilan.store');

    // Nilai Sikap (C3) - scoped to wali kelas's class
    Route::get('nilaisikap', [\App\Http\Controllers\WaliKelas\NilaiSikapController::class, 'index'])
        ->name('walikelas.nilaisikap.index');
    Route::post('nilaisikap', [\App\Http\Controllers\WaliKelas\NilaiSikapController::class, 'store'])
        ->name('walikelas.nilaisikap.store');

    // Nilai Ekstrakurikuler (C4) - scoped to wali kelas's class
    Route::get('nilaiekstrakurikuler', [\App\Http\Controllers\WaliKelas\NilaiEkstrakurikulerController::class, 'index'])
        ->name('walikelas.nilaiekstrakurikuler.index');
    Route::get('nilaiekstrakurikuler/create', [\App\Http\Controllers\WaliKelas\NilaiEkstrakurikulerController::class, 'create'])
        ->name('walikelas.nilaiekstrakurikuler.create');
    Route::post('nilaiekstrakurikuler', [\App\Http\Controllers\WaliKelas\NilaiEkstrakurikulerController::class, 'store'])
        ->name('walikelas.nilaiekstrakurikuler.store');
    Route::get('nilaiekstrakurikuler/get-ekskul', [\App\Http\Controllers\WaliKelas\NilaiEkstrakurikulerController::class, 'getEkskul'])
        ->name('walikelas.nilaiekstrakurikuler.getEkskul');
    Route::delete('nilaiekstrakurikuler/{id}', [\App\Http\Controllers\WaliKelas\NilaiEkstrakurikulerController::class, 'destroy'])
        ->name('walikelas.nilaiekstrakurikuler.destroy');

    // Nilai Absensi (C6) - scoped to wali kelas's class
    Route::get('nilaiabsensi', [\App\Http\Controllers\WaliKelas\NilaiAbsensiController::class, 'index'])
        ->name('walikelas.nilaiabsensi.index');
    Route::post('nilaiabsensi', [\App\Http\Controllers\WaliKelas\NilaiAbsensiController::class, 'store'])
        ->name('walikelas.nilaiabsensi.store');

    // Perhitungan SMART (standalone, scoped to wali kelas's class)
    Route::prefix('perhitungan/smart')->name('walikelas.perhitungan.smart.')->group(function () {
        Route::get('/', [\App\Http\Controllers\WaliKelas\PerhitunganController::class, 'indexSmart'])->name('index');
        Route::post('/calculate', [\App\Http\Controllers\WaliKelas\PerhitunganController::class, 'calculateSmart'])->name('calculate');
        Route::get('/steps/{id_ta}', [\App\Http\Controllers\WaliKelas\PerhitunganController::class, 'showStepsSmart'])->name('steps');
    });

    // Perhitungan MOORA (standalone, scoped to wali kelas's class)
    Route::prefix('perhitungan/moora')->name('walikelas.perhitungan.moora.')->group(function () {
        Route::get('/', [\App\Http\Controllers\WaliKelas\PerhitunganController::class, 'indexMoora'])->name('index');
        Route::post('/calculate', [\App\Http\Controllers\WaliKelas\PerhitunganController::class, 'calculateMoora'])->name('calculate');
        Route::get('/steps/{id_ta}', [\App\Http\Controllers\WaliKelas\PerhitunganController::class, 'showStepsMoora'])->name('steps');
    });

    // Perbandingan SMART vs MOORA
    Route::get('perhitungan/compare/{id_ta}', [\App\Http\Controllers\WaliKelas\PerhitunganController::class, 'compare'])
        ->name('walikelas.perhitungan.compare');
});

/*
|--------------------------------------------------------------------------
| Kepala Sekolah Routes
|--------------------------------------------------------------------------
*/
Route::prefix('kepala-sekolah')->middleware(['auth', 'check.level:Kepala Sekolah'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\KepalaSekolah\DashboardController::class, 'index'])
        ->name('kepalasekolah.dashboard');

    // Siswa (read-only)
    Route::get('siswa', [\App\Http\Controllers\KepalaSekolah\SiswaController::class, 'index'])
        ->name('kepalasekolah.siswa.index');

    // Kelas (read-only)
    Route::get('kelas', [\App\Http\Controllers\KepalaSekolah\KelasController::class, 'index'])
        ->name('kepalasekolah.kelas.index');

    // Kriteria (read-only)
    Route::get('kriteria', [\App\Http\Controllers\KepalaSekolah\KriteriaController::class, 'index'])
        ->name('kepalasekolah.kriteria.index');

    // Mata Pelajaran (read-only)
    Route::get('matapelajaran', [\App\Http\Controllers\KepalaSekolah\MataPelajaranController::class, 'index'])
        ->name('kepalasekolah.matapelajaran.index');

    // Penilaian (read-only)
    Route::get('penilaian', [\App\Http\Controllers\KepalaSekolah\PenilaianController::class, 'index'])
        ->name('kepalasekolah.penilaian.index');
    Route::get('penilaian/{penilaian}', [\App\Http\Controllers\KepalaSekolah\PenilaianController::class, 'show'])
        ->name('kepalasekolah.penilaian.show');

    // Perangkingan / Hasil Perhitungan
    Route::get('perhitungan/smart',  [\App\Http\Controllers\KepalaSekolah\PerhitunganController::class, 'indexSmart'])->name('kepalasekolah.perhitungan.smart.index');
    Route::get('perhitungan/moora',  [\App\Http\Controllers\KepalaSekolah\PerhitunganController::class, 'indexMoora'])->name('kepalasekolah.perhitungan.moora.index');

    // Report / Export
    Route::get('report/pdf', [\App\Http\Controllers\KepalaSekolah\ReportController::class, 'exportPdf'])
        ->name('kepalasekolah.report.pdf');
    Route::get('report/excel', [\App\Http\Controllers\KepalaSekolah\ReportController::class, 'exportExcel'])
        ->name('kepalasekolah.report.excel');
});
