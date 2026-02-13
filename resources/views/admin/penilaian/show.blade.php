@extends('layouts.admin')
@section('title', 'Detail Penilaian Siswa')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.penilaian.index') }}">Penilaian Siswa</a>
                </li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Info Siswa -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <h5 class="card-header">Info Siswa</h5>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-semibold">NISN</td>
                                <td>: {{ $siswa->nisn }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Nama</td>
                                <td>: {{ $siswa->nama_siswa }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Kelas</td>
                                <td>: {{ $siswa->kelas ? $siswa->kelas->nama_kelas : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Jenis Kelamin</td>
                                <td>: {{ $siswa->jenis_kelamin }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <h5 class="card-header">Aksi</h5>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.penilaian.index') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i> Kembali
                            </a>
                            <a href="{{ route('admin.penilaian.edit', ['penilaian' => $siswa->id_siswa, 'ta' => $filterTA]) }}"
                                class="btn btn-primary">
                                <i class="bx bx-edit me-1"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Penilaian -->
            <div class="col-md-8">
                <div class="card">
                    <h5 class="card-header">Detail Penilaian</h5>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode</th>
                                        <th>Kriteria</th>
                                        <th>Jenis</th>
                                        <th>Bobot</th>
                                        <th class="text-end">Nilai Asli</th>
                                        <th class="text-end">Nilai Konversi</th>
                                        <th class="text-end">Weighted</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalWeighted = 0;
                                    @endphp
                                    @foreach ($kriteriaList as $kriteria)
                                        @php
                                            $penilaian = $penilaianList[$kriteria->id_kriteria] ?? null;
                                            $weighted =
                                                $penilaian && $penilaian->nilai_konversi
                                                    ? $penilaian->nilai_konversi * $kriteria->bobot
                                                    : 0;
                                            $totalWeighted += $weighted;
                                        @endphp
                                        <tr class="{{ $kriteria->kode_kriteria == 'C5' ? 'table-warning' : '' }}">
                                            <td>
                                                <strong>{{ $kriteria->kode_kriteria }}</strong>
                                                @if ($kriteria->kode_kriteria == 'C5')
                                                    <i class="bx bx-bolt text-warning" title="Auto"></i>
                                                @endif
                                            </td>
                                            <td>{{ $kriteria->nama_kriteria }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-label-{{ $kriteria->jenis_kriteria == 'Benefit' ? 'success' : 'warning' }}">
                                                    {{ $kriteria->jenis_kriteria }}
                                                </span>
                                            </td>
                                            <td>{{ $kriteria->bobot }}</td>
                                            <td class="text-end">
                                                @if ($penilaian)
                                                    <strong>{{ $penilaian->nilai_asli }}</strong>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if ($penilaian)
                                                    <span
                                                        class="badge bg-label-primary">{{ $penilaian->nilai_konversi }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if ($penilaian)
                                                    <strong>{{ number_format($weighted, 2) }}</strong>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="6" class="text-end"><strong>Total Weighted Score:</strong></td>
                                        <td class="text-end">
                                            <strong
                                                class="text-primary fs-5">{{ number_format($totalWeighted, 2) }}</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="mt-3">
                            <h6 class="text-muted">Keterangan:</h6>
                            <ul class="small text-muted mb-0">
                                <li><strong>Nilai Asli:</strong> Nilai mentah yang diinputkan (0-100)</li>
                                <li><strong>Nilai Konversi:</strong> Nilai yang sudah dikonversi berdasarkan sub kriteria
                                </li>
                                <li><strong>Weighted:</strong> Nilai Konversi × Bobot Kriteria</li>
                                <li><i class="bx bx-bolt text-warning"></i> <strong>C5</strong> dihitung otomatis dari total
                                    poin pelanggaran</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
