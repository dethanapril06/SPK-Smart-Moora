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
            {{-- Top 10 Finalis Ranking --}}
            <div class="col-lg-8 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0">
                            <i class="bx bx-trophy text-warning"></i> Top 10 Ranking Finalis
                        </h5>
                        @if ($hasCalculation)
                            <a href="{{ route('admin.perhitungan.finalis.smart.index', ['tahun_ajaran' => $tahunAjaranAktif->id_ta]) }}"
                                class="btn btn-sm btn-label-primary">
                                Lihat Semua
                            </a>
                        @endif
                    </div>
                    @if ($topFinalisSmartByTingkat->flatten(1)->count() > 0 || $topFinalisMooraByTingkat->flatten(1)->count() > 0)
                        @php $tingkatList = ['X', 'XI', 'XII']; @endphp
                        <div class="nav-align-top">
                            <ul class="nav nav-tabs" role="tablist">
                                @foreach ($tingkatList as $index => $tingkat)
                                    <li class="nav-item" role="presentation">
                                        <button type="button" class="nav-link {{ $index === 0 ? 'active' : '' }}"
                                            role="tab" data-bs-toggle="tab"
                                            data-bs-target="#dashboard-finalis-{{ strtolower($tingkat) }}">
                                            Kelas {{ $tingkat }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content p-0">
                                @foreach ($tingkatList as $index => $tingkat)
                                    @php
                                        $topFinalisSmart = $topFinalisSmartByTingkat->get($tingkat, collect());
                                        $topFinalisMoora = $topFinalisMooraByTingkat->get($tingkat, collect());
                                    @endphp
                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                                        id="dashboard-finalis-{{ strtolower($tingkat) }}" role="tabpanel">
                                        <div class="table-responsive top-finalis-scroll">
                                            <table class="table table-hover top-finalis-table mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th colspan="4" class="text-center text-primary border-end">SMART</th>
                                                        <th colspan="4" class="text-center text-success">MOORA</th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-center" width="70">Rank</th>
                                                        <th>Siswa</th>
                                                        <th class="text-center">Kelas</th>
                                                        <th class="text-center border-end">Skor</th>
                                                        <th class="text-center" width="70">Rank</th>
                                                        <th>Siswa</th>
                                                        <th class="text-center">Kelas</th>
                                                        <th class="text-center">Skor</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @for ($i = 0; $i < 10; $i++)
                                                        @php
                                                            $smart = $topFinalisSmart->get($i);
                                                            $moora = $topFinalisMoora->get($i);
                                                        @endphp
                                                        <tr>
                                                            @if ($smart)
                                                                <td class="text-center"><span class="badge {{ $smart->rank == 1 ? 'bg-warning' : 'bg-label-dark' }}">{{ $smart->rank }}</span></td>
                                                                <td><strong>{{ $smart->siswa->nisn }}</strong><br><small>{{ $smart->siswa->nama_siswa }}</small></td>
                                                                <td class="text-center"><span class="badge bg-label-info">{{ $smart->siswa->kelas->nama_kelas ?? '-' }}</span></td>
                                                                <td class="text-center border-end"><strong class="text-primary">{{ number_format($smart->skor, 4) }}</strong></td>
                                                            @else
                                                                <td class="text-center text-muted">-</td><td class="text-muted">-</td><td class="text-center text-muted">-</td><td class="text-center border-end text-muted">-</td>
                                                            @endif

                                                            @if ($moora)
                                                                <td class="text-center"><span class="badge {{ $moora->rank == 1 ? 'bg-warning' : 'bg-label-dark' }}">{{ $moora->rank }}</span></td>
                                                                <td><strong>{{ $moora->siswa->nisn }}</strong><br><small>{{ $moora->siswa->nama_siswa }}</small></td>
                                                                <td class="text-center"><span class="badge bg-label-info">{{ $moora->siswa->kelas->nama_kelas ?? '-' }}</span></td>
                                                                <td class="text-center"><strong class="text-success">{{ number_format($moora->skor, 4) }}</strong></td>
                                                            @else
                                                                <td class="text-center text-muted">-</td><td class="text-muted">-</td><td class="text-center text-muted">-</td><td class="text-center text-muted">-</td>
                                                            @endif
                                                        </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
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
                                <a href="{{ route('admin.perhitungan.finalis.smart.index', ['tahun_ajaran' => $tahunAjaranAktif->id_ta]) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="bx bx-calculator"></i> Ke Halaman 10 Besar
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
                            <div class="siswa-per-kelas-scroll">
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
                            </div>
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

@push('scripts')
    <style>
        .siswa-per-kelas-scroll {
            max-height: 730px;
            overflow-y: auto;
            padding-right: 0.25rem;
        }

        .top-finalis-scroll {
            overflow-x: auto;
        }

        .top-finalis-table {
            min-width: 1180px;
        }

        .top-finalis-table th,
        .top-finalis-table td {
            vertical-align: middle;
            white-space: nowrap;
        }
    </style>
@endpush
