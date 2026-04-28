@extends('layouts.admin')
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
                                    height="140" alt="SPK Smart Moora"
                                    data-app-dark-img="{{ asset('template/assets/img/illustrations/man-with-laptop-dark.png') }}"
                                    data-app-light-img="{{ asset('template/assets/img/illustrations/man-with-laptop-light.png') }}" />
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
                                <small class="text-muted fw-semibold">
                                    <i class="bx bx-user"></i> Data keseluruhan
                                </small>
                            </div>
                            <div class="avatar flex-shrink-0">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-group bx-sm"></i>
                                </span>
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
                                <small class="text-muted fw-semibold">
                                    <i class="bx bx-chalkboard"></i> Kelas terdaftar
                                </small>
                            </div>
                            <div class="avatar flex-shrink-0">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="bx bx-chalkboard bx-sm"></i>
                                </span>
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
                                <span class="fw-semibold d-block mb-1">Total Kriteria</span>
                                <h3 class="card-title mb-2">{{ $totalKriteria }}</h3>
                                <small class="text-muted fw-semibold">
                                    <i class="bx bx-list-check"></i> Kriteria penilaian
                                </small>
                            </div>
                            <div class="avatar flex-shrink-0">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="bx bx-list-check bx-sm"></i>
                                </span>
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
                                <span class="fw-semibold d-block mb-1">Total Pengguna</span>
                                <h3 class="card-title mb-2">{{ $totalPengguna }}</h3>
                                <small class="text-muted fw-semibold">
                                    <i class="bx bx-user-circle"></i> Akun pengguna
                                </small>
                            </div>
                            <div class="avatar flex-shrink-0">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="bx bx-user-circle bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Penilaian & Pelanggaran --}}
        @if ($tahunAjaranAktif)
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <span class="fw-semibold d-block mb-1">Sudah Dinilai</span>
                                    <h3 class="card-title text-success mb-2">{{ $siswadinilai }}</h3>
                                    <small class="text-success fw-semibold">
                                        <i class="bx bx-check-circle"></i> Penilaian lengkap
                                    </small>
                                </div>
                                <div class="avatar flex-shrink-0">
                                    <span class="avatar-initial rounded bg-label-success">
                                        <i class="bx bx-check-double bx-sm"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <span class="fw-semibold d-block mb-1">Belum Dinilai</span>
                                    <h3 class="card-title text-danger mb-2">{{ $siswaBelumDinilai }}</h3>
                                    <small class="text-danger fw-semibold">
                                        <i class="bx bx-x-circle"></i> Penilaian belum lengkap
                                    </small>
                                </div>
                                <div class="avatar flex-shrink-0">
                                    <span class="avatar-initial rounded bg-label-danger">
                                        <i class="bx bx-time-five bx-sm"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <span class="fw-semibold d-block mb-1">Total Pelanggaran</span>
                                    <h3 class="card-title text-warning mb-2">{{ $totalPelanggaran }}</h3>
                                    <small class="text-warning fw-semibold">
                                        <i class="bx bx-error"></i> Tahun ajaran aktif
                                    </small>
                                </div>
                                <div class="avatar flex-shrink-0">
                                    <span class="avatar-initial rounded bg-label-warning">
                                        <i class="bx bx-error bx-sm"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            {{-- Top 5 Siswa Ranking --}}
            <div class="col-lg-8 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0">
                            <i class="bx bx-trophy text-warning"></i> Top 5 Ranking Siswa
                        </h5>
                        @if ($hasCalculation)
                            <a href="{{ route('admin.perhitungan.smart.index', ['tahun_ajaran' => $tahunAjaranAktif->id_ta]) }}"
                                class="btn btn-sm btn-label-primary">
                                Lihat Semua
                            </a>
                        @endif
                    </div>
                    @if ($topSiswa->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" width="60">Rank</th>
                                        <th>NISN / Nama Siswa</th>
                                        <th class="text-center">Kelas</th>
                                        <th class="text-center">Skor SMART</th>
                                        <th class="text-center">Skor MOORA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($topSiswa as $item)
                                        <tr>
                                            <td class="text-center">
                                                @if ($item->rank_smart == 1)
                                                    <span class="badge bg-warning"><i class="bx bx-trophy"></i>
                                                        1</span>
                                                @elseif($item->rank_smart == 2)
                                                    <span class="badge bg-secondary">2</span>
                                                @elseif($item->rank_smart == 3)
                                                    <span class="badge bg-label-warning">3</span>
                                                @else
                                                    <span class="badge bg-label-dark">{{ $item->rank_smart }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $item->siswa->nisn }}</strong><br>
                                                <small>{{ $item->siswa->nama_siswa }}</small>
                                            </td>
                                            <td class="text-center">
                                                @if ($item->siswa->kelas)
                                                    <span
                                                        class="badge bg-label-info">{{ $item->siswa->kelas->nama_kelas }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <strong
                                                    class="text-primary">{{ number_format($item->skor_smart, 4) }}</strong>
                                            </td>
                                            <td class="text-center">
                                                <strong
                                                    class="text-success">{{ number_format($item->skor_moora, 4) }}</strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="card-body text-center py-5">
                            <i class="bx bx-calculator bx-lg text-muted mb-3 d-block"></i>
                            <h6 class="text-muted">Belum Ada Data Ranking</h6>
                            <p class="text-muted mb-3">
                                @if ($tahunAjaranAktif)
                                    Silahkan lakukan perhitungan terlebih dahulu.
                                @else
                                    Pilih tahun ajaran aktif terlebih dahulu.
                                @endif
                            </p>
                            @if ($tahunAjaranAktif)
                                <a href="{{ route('admin.perhitungan.smart.index', ['tahun_ajaran' => $tahunAjaranAktif->id_ta]) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="bx bx-calculator"></i> Ke Halaman Perhitungan
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Distribusi Siswa Per Kelas --}}
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0">
                            <i class="bx bx-bar-chart-alt-2 text-info"></i> Siswa Per Kelas
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($siswaPerKelas->count() > 0)
                            @foreach ($siswaPerKelas as $kelas)
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-label-primary me-2">
                                            <i class="bx bx-chalkboard"></i>
                                        </span>
                                        <span class="fw-semibold">{{ $kelas->nama_kelas }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-label-info rounded-pill">{{ $kelas->siswa_count }}
                                            siswa</span>
                                    </div>
                                </div>
                                <div class="progress mb-3" style="height: 6px;">
                                    <div class="progress-bar bg-primary" role="progressbar"
                                        style="width: {{ $kelas->kapasitas > 0 ? round(($kelas->siswa_count / $kelas->kapasitas) * 100) : 0 }}%"
                                        aria-valuenow="{{ $kelas->siswa_count }}" aria-valuemin="0"
                                        aria-valuemax="{{ $kelas->kapasitas }}">
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <i class="bx bx-chalkboard bx-lg text-muted mb-2 d-block"></i>
                                <p class="text-muted mb-0">Belum ada data kelas</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Bobot Kriteria --}}
            <div class="col-lg-5 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title m-0">
                            <i class="bx bx-list-check text-warning"></i> Bobot Kriteria
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($kriteriaList->count() > 0)
                            @php
                                $colors = ['primary', 'success', 'warning', 'info', 'danger', 'secondary'];
                            @endphp
                            @foreach ($kriteriaList as $index => $kriteria)
                                @php
                                    $color = $colors[$index % count($colors)];
                                    $percentage =
                                        $totalBobot > 0 ? round(($kriteria->bobot / $totalBobot) * 100, 1) : 0;
                                @endphp
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <span
                                            class="badge bg-label-{{ $color }} me-1">{{ $kriteria->kode_kriteria }}</span>
                                        <small>{{ $kriteria->nama_kriteria }}</small>
                                    </div>
                                    <div class="text-end">
                                        <small class="fw-semibold">{{ $kriteria->bobot }}</small>
                                        <small class="text-muted">({{ $percentage }}%)</small>
                                    </div>
                                </div>
                                <div class="progress mb-3" style="height: 5px;">
                                    <div class="progress-bar bg-{{ $color }}" role="progressbar"
                                        style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}"
                                        aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            @endforeach
                            <div class="d-flex justify-content-between border-top pt-2 mt-2">
                                <strong>Total Bobot</strong>
                                <strong>{{ $totalBobot }}</strong>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bx bx-list-check bx-lg text-muted mb-2 d-block"></i>
                                <p class="text-muted mb-0">Belum ada data kriteria</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Pelanggaran Terbaru --}}
            <div class="col-lg-7 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0">
                            <i class="bx bx-error-circle text-danger"></i> Pelanggaran Terbaru
                        </h5>
                        <a href="{{ route('admin.riwayatpelanggaran.index') }}" class="btn btn-sm btn-label-danger">
                            Lihat Semua
                        </a>
                    </div>
                    @if ($pelanggaranTerbaru->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
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
                                    @foreach ($pelanggaranTerbaru as $pelanggaran)
                                        <tr>
                                            <td>
                                                <small>{{ $pelanggaran->tanggal_kejadian->format('d M Y') }}</small>
                                            </td>
                                            <td>
                                                <small
                                                    class="fw-semibold">{{ $pelanggaran->siswa->nama_siswa ?? '-' }}</small>
                                            </td>
                                            <td>
                                                <small
                                                    class="fw-semibold">{{ $pelanggaran->siswa->kelas->nama_kelas }}</small>
                                            </td>
                                            <td>
                                                <small>{{ $pelanggaran->jenisPelanggaran->nama_pelanggaran ?? '-' }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-label-danger">
                                                    {{ $pelanggaran->jenisPelanggaran->bobot_poin ?? 0 }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="card-body text-center py-5">
                            <i class="bx bx-check-shield bx-lg text-success mb-3 d-block"></i>
                            <h6 class="text-muted">Tidak Ada Pelanggaran</h6>
                            <p class="text-muted mb-0">Belum ada data pelanggaran pada tahun ajaran ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
