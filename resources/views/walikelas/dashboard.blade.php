@extends('layouts.walikelas')
@section('title', 'Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @if (isset($noKelas) && $noKelas)
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-warning">
                        <h5 class="alert-heading"><i class="bx bx-error"></i> Belum Ditugaskan</h5>
                        <p class="mb-0">Anda belum ditugaskan sebagai wali kelas. Silakan hubungi admin untuk penugasan
                            kelas.</p>
                    </div>
                </div>
            </div>
        @else
            {{-- Welcome Banner --}}
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="d-flex align-items-end row">
                            <div class="col-sm-7">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">Selamat Datang, {{ Auth::user()->name }}!</h5>
                                    <p class="mb-2">
                                        Wali Kelas <strong>{{ $kelas->nama_kelas }}</strong> - SPK menggunakan metode
                                        <strong>SMART</strong> & <strong>MOORA</strong>
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
                <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <span class="fw-semibold d-block mb-1">Siswa di Kelas</span>
                                    <h3 class="card-title mb-2">{{ $totalSiswa }}</h3>
                                    <small class="text-muted fw-semibold">
                                        <i class="bx bx-user"></i> {{ $kelas->nama_kelas }}
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

                <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <span class="fw-semibold d-block mb-1">Sudah Dinilai</span>
                                    <h3 class="card-title mb-2">{{ $siswadinilai }}</h3>
                                    <small class="text-muted fw-semibold">
                                        <i class="bx bx-check-circle"></i> Belum: {{ $siswaBelumDinilai }}
                                    </small>
                                </div>
                                <div class="avatar flex-shrink-0">
                                    <span class="avatar-initial rounded bg-label-success">
                                        <i class="bx bx-star bx-sm"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <span class="fw-semibold d-block mb-1">Total Pelanggaran</span>
                                    <h3 class="card-title mb-2">{{ $totalPelanggaran }}</h3>
                                    <small class="text-muted fw-semibold">
                                        <i class="bx bx-error"></i> TA Aktif
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

            {{-- Top 5 Siswa SMART dan MOORA --}}
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <h5 class="card-header">Top 5 Siswa (SMART)</h5>
                        @if ($topSmart->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Rank</th>
                                            <th>Nama Siswa</th>
                                            <th>Skor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($topSmart as $item)
                                            <tr>
                                                <td><span class="badge bg-label-primary">{{ $item->rank_smart }}</span>
                                                </td>
                                                <td><small class="fw-semibold">{{ $item->siswa->nama_siswa }}</small></td>
                                                <td><small>{{ number_format($item->skor_smart, 4) }}</small></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="card-body text-center py-5">
                                <i class="bx bx-calculator bx-lg text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">Belum ada perhitungan ranking.</p>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <h5 class="card-header">Top 5 Siswa (MOORA)</h5>
                        @if ($topMoora->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Rank</th>
                                            <th>Nama Siswa</th>
                                            <th>Skor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($topMoora as $item)
                                            <tr>
                                                <td><span class="badge bg-label-primary">{{ $item->rank_moora }}</span>
                                                </td>
                                                <td><small class="fw-semibold">{{ $item->siswa->nama_siswa }}</small></td>
                                                <td><small>{{ number_format($item->skor_moora, 4) }}</small></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="card-body text-center py-5">
                                <i class="bx bx-calculator bx-lg text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">Belum ada perhitungan ranking.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-12 mb-4">
                    <div class="card h-100">
                        <h5 class="card-header">Pelanggaran Terbaru</h5>
                        @if ($pelanggaranTerbaru->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Siswa</th>
                                            <th>Pelanggaran</th>
                                            <th>Poin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pelanggaranTerbaru as $pelanggaran)
                                            <tr>
                                                <td><small>{{ $pelanggaran->tanggal_kejadian->format('d M Y') }}</small>
                                                </td>
                                                <td><small
                                                        class="fw-semibold">{{ $pelanggaran->siswa->nama_siswa ?? '-' }}</small>
                                                </td>
                                                <td><small>{{ $pelanggaran->jenisPelanggaran->nama_pelanggaran ?? '-' }}</small>
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge bg-label-danger">{{ $pelanggaran->jenisPelanggaran->bobot_poin ?? 0 }}</span>
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
        @endif
    </div>
@endsection
