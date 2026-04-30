@extends('layouts.kepalasekolah')
@section('title', 'Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Welcome Banner --}}
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-7">
                            <div class="card-body">
                                <h5 class="card-title text-primary">Selamat Datang, {{ Auth::user()->name }}!</h5>
                                <p class="mb-4">
                                    Sistem Pendukung Keputusan menggunakan metode
                                    <strong>SMART</strong> & <strong>MOORA</strong> untuk menentukan siswa berprestasi.
                                </p>
                                @if ($tahunAjaranAktif)
                                    <span class="badge bg-label-primary rounded-pill">
                                        <i class="bx bx-calendar"></i>
                                        {{ $tahunAjaranAktif->tahun_ajaran }} - {{ $tahunAjaranAktif->semester }}
                                    </span>
                                @else
                                    <span class="badge bg-label-warning rounded-pill">
                                        <i class="bx bx-calendar-x"></i> Belum ada tahun ajaran aktif
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-5 text-center text-sm-left">
                            <div class="card-body pb-0 px-0 px-md-4">
                                <img src="{{ asset('template/assets/img/illustrations/man-with-laptop-light.png') }}"
                                    height="140" alt="SPK Smart Moora" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistik Cards --}}
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <span class="fw-semibold d-block mb-1">Total Siswa</span>
                                <h3 class="card-title mb-2">{{ $totalSiswa }}</h3>
                                <small class="text-muted fw-semibold"><i class="bx bx-user"></i> Data keseluruhan</small>
                            </div>
                            <div class="avatar flex-shrink-0">
                                <span class="avatar-initial rounded bg-label-primary"><i
                                        class="bx bx-group bx-sm"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <span class="fw-semibold d-block mb-1">Total Kelas</span>
                                <h3 class="card-title mb-2">{{ $totalKelas }}</h3>
                                <small class="text-muted fw-semibold"><i class="bx bx-chalkboard"></i> Kelas
                                    terdaftar</small>
                            </div>
                            <div class="avatar flex-shrink-0">
                                <span class="avatar-initial rounded bg-label-success"><i
                                        class="bx bx-chalkboard bx-sm"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <span class="fw-semibold d-block mb-1">Sudah Dinilai</span>
                                <h3 class="card-title mb-2">{{ $siswadinilai }}</h3>
                                <small class="text-muted fw-semibold"><i class="bx bx-check-circle"></i> Belum:
                                    {{ $siswaBelumDinilai }}</small>
                            </div>
                            <div class="avatar flex-shrink-0">
                                <span class="avatar-initial rounded bg-label-warning"><i
                                        class="bx bx-star bx-sm"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <span class="fw-semibold d-block mb-1">Total Pelanggaran</span>
                                <h3 class="card-title mb-2">{{ $totalPelanggaran }}</h3>
                                <small class="text-muted fw-semibold"><i class="bx bx-error"></i> TA Aktif</small>
                            </div>
                            <div class="avatar flex-shrink-0">
                                <span class="avatar-initial rounded bg-label-danger"><i
                                        class="bx bx-error bx-sm"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Perhitungan --}}
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bx bx-calculator bx-lg text-primary mb-2 d-block"></i>
                        <h3 class="mb-1">{{ $adminCount }}</h3>
                        <p class="text-muted mb-3">Perhitungan oleh Admin</p>
                        <a href="{{ route('kepalasekolah.perhitungan.smart.index', ['source' => 'admin']) }}"
                            class="btn btn-sm btn-label-primary">
                            <i class="bx bx-show"></i> Lihat Hasil
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bx bx-user-check bx-lg text-success mb-2 d-block"></i>
                        <h3 class="mb-1">{{ $waliKelasCount }}</h3>
                        <p class="text-muted mb-3">Wali Kelas Sudah Menghitung</p>
                        <a href="{{ route('kepalasekolah.perhitungan.smart.index', ['source' => 'wali_kelas']) }}"
                            class="btn btn-sm btn-label-success">
                            <i class="bx bx-show"></i> Lihat Hasil
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Distribusi Siswa Per Kelas --}}
            <div class="col-lg-5 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title m-0"><i class="bx bx-bar-chart-alt-2 text-info"></i> Siswa Per Kelas</h5>
                    </div>
                    <div class="card-body">
                        @foreach ($siswaPerKelas as $kelas)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-label-primary me-2"><i class="bx bx-chalkboard"></i></span>
                                    <span class="fw-semibold">{{ $kelas->nama_kelas }}</span>
                                </div>
                                <span class="badge bg-label-info rounded-pill">{{ $kelas->siswa_count }} siswa</span>
                            </div>
                            <div class="progress mb-3" style="height: 6px;">
                                <div class="progress-bar bg-primary" role="progressbar"
                                    style="width: {{ $kelas->kapasitas > 0 ? round(($kelas->siswa_count / $kelas->kapasitas) * 100) : 0 }}%">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Pelanggaran Terbaru --}}
            <div class="col-lg-7 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title m-0"><i class="bx bx-error-circle text-danger"></i> Pelanggaran Terbaru</h5>
                    </div>
                    @if ($pelanggaranTerbaru->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Siswa</th>
                                        <th>Kelas</th>
                                        <th>Pelanggaran</th>
                                        <th class="text-center">Poin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pelanggaranTerbaru as $p)
                                        <tr>
                                            <td><small>{{ $p->tanggal_kejadian->format('d M Y') }}</small></td>
                                            <td><small class="fw-semibold">{{ $p->siswa->nama_siswa ?? '-' }}</small></td>
                                            <td><small>{{ $p->siswa->kelas->nama_kelas ?? '-' }}</small></td>
                                            <td><small>{{ $p->jenisPelanggaran->nama_pelanggaran ?? '-' }}</small></td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-label-danger">{{ $p->jenisPelanggaran->bobot_poin ?? 0 }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="card-body text-center py-5">
                            <i class="bx bx-check-shield bx-lg text-success mb-3 d-block"></i>
                            <p class="text-muted mb-0">Tidak ada pelanggaran.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
