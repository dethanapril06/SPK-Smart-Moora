@extends('layouts.walikelas')
@section('title', 'Detail Riwayat Pelanggaran')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('walikelas.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('walikelas.riwayatpelanggaran.index') }}">Riwayat
                        Pelanggaran</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <h5 class="card-header">Detail Pelanggaran</h5>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-semibold" style="width: 200px;">Tanggal Kejadian</td>
                                <td>: {{ $riwayatpelanggaran->tanggal_kejadian->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Tahun Ajaran</td>
                                <td>:
                                    @if ($riwayatpelanggaran->tahunAjaran)
                                        {{ $riwayatpelanggaran->tahunAjaran->tahun_ajaran }} -
                                        {{ $riwayatpelanggaran->tahunAjaran->semester }}
                                        @if ($riwayatpelanggaran->tahunAjaran->is_active)
                                            <span class="badge bg-label-success">Aktif</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Kategori</td>
                                <td>:
                                    @if ($riwayatpelanggaran->jenisPelanggaran)
                                        <span
                                            class="badge bg-label-primary">{{ str_replace('_', ' ', $riwayatpelanggaran->jenisPelanggaran->kategori_pelanggaran) }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Jenis Pelanggaran</td>
                                <td>: {{ $riwayatpelanggaran->jenisPelanggaran->nama_pelanggaran ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Bobot Poin</td>
                                <td>:
                                    @if ($riwayatpelanggaran->jenisPelanggaran)
                                        @php
                                            $poin = $riwayatpelanggaran->jenisPelanggaran->bobot_poin;
                                            $badgeClass = $poin >= 20 ? 'danger' : ($poin >= 10 ? 'warning' : 'info');
                                        @endphp
                                        <span class="badge bg-label-{{ $badgeClass }}">{{ $poin }} poin</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Keterangan</td>
                                <td>: {{ $riwayatpelanggaran->keterangan_tambahan ?? '-' }}</td>
                            </tr>
                        </table>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('walikelas.riwayatpelanggaran.index') }}" class="btn btn-secondary"><i
                                    class="bx bx-arrow-back me-1"></i> Kembali</a>
                            <a href="{{ route('walikelas.riwayatpelanggaran.edit', $riwayatpelanggaran->id_riwayat) }}"
                                class="btn btn-primary"><i class="bx bx-edit me-1"></i> Edit</a>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h5 class="card-header">Riwayat Pelanggaran Siswa
                        ({{ $riwayatpelanggaran->tahunAjaran ? $riwayatpelanggaran->tahunAjaran->tahun_ajaran : '-' }})
                    </h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Pelanggaran</th>
                                    <th>Poin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($riwayatSiswa as $item)
                                    <tr
                                        class="{{ $item->id_riwayat == $riwayatpelanggaran->id_riwayat ? 'table-primary' : '' }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->tanggal_kejadian->format('d/m/Y') }}</td>
                                        <td>
                                            {{ Str::limit($item->jenisPelanggaran->nama_pelanggaran, 50) }}
                                            @if ($item->id_riwayat == $riwayatpelanggaran->id_riwayat)
                                                <span class="badge bg-label-info">Saat Ini</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $poin = $item->jenisPelanggaran->bobot_poin;
                                                $badgeClass =
                                                    $poin >= 20 ? 'danger' : ($poin >= 10 ? 'warning' : 'info');
                                            @endphp
                                            <span class="badge bg-label-{{ $badgeClass }}">{{ $poin }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <h5 class="card-header">Info Siswa</h5>
                    <div class="card-body">
                        @if ($riwayatpelanggaran->siswa)
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-semibold">NISN</td>
                                    <td>: {{ $riwayatpelanggaran->siswa->nisn }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Nama</td>
                                    <td>: {{ $riwayatpelanggaran->siswa->nama_siswa }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Kelas</td>
                                    <td>:
                                        @if ($riwayatpelanggaran->siswa->kelas)
                                            <span
                                                class="badge bg-label-info">{{ $riwayatpelanggaran->siswa->kelas->nama_kelas }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Jenis Kelamin</td>
                                    <td>: {{ $riwayatpelanggaran->siswa->jenis_kelamin }}</td>
                                </tr>
                            </table>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <h5 class="card-header">Statistik Pelanggaran</h5>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-error"></i></span>
                            </div>
                            <div>
                                <small class="text-muted d-block">Total Pelanggaran</small>
                                <h5 class="mb-0">{{ $riwayatSiswa->count() }}</h5>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-star"></i></span>
                            </div>
                            <div>
                                <small class="text-muted d-block">Total Poin</small>
                                <h5 class="mb-0">{{ $totalPoin }} poin</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
