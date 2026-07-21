@extends('layouts.walikelas')
@section('title', 'Kelas Saya')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('walikelas.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Kelas Saya</li>
            </ol>
        </nav>

        @if ($kelas)
            <div class="d-flex gap-2 mb-4">
                @if(str_starts_with($kelas->id_kelas, 'XII.'))
                    <a href="{{ route('walikelas.kelas.kelulusan.index') }}" class="btn btn-success btn-sm">
                        <i class="bx bx-graduation me-1"></i> Kelulusan Siswa
                    </a>
                @elseif(preg_match('/^(X|XI)\./', $kelas->id_kelas))
                    <a href="{{ route('walikelas.kelas.naik-kelas.index') }}" class="btn btn-warning btn-sm">
                        <i class="bx bx-transfer-alt me-1"></i> Naik Kelas
                    </a>
                @endif
            </div>
        @endif

        @if (!$kelas)
            <div class="alert alert-warning">
                <i class="bx bx-error"></i> Anda belum ditugaskan sebagai wali kelas.
            </div>
        @else
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <h5 class="card-header">Informasi Kelas</h5>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-semibold" style="width:150px;">ID Kelas</td>
                                    <td>: {{ $kelas->id_kelas }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Nama Kelas</td>
                                    <td>: <strong>{{ $kelas->nama_kelas }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Wali Kelas</td>
                                    <td>: {{ $kelas->waliKelas->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Kapasitas</td>
                                    <td>: {{ $kelas->kapasitas ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Jumlah Siswa</td>
                                    <td>: <span class="badge bg-label-primary">{{ $kelas->siswa->count() }}</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <h5 class="card-header">Daftar Siswa</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>NISN</th>
                                        <th>Nama</th>
                                        <th>JK</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($kelas->siswa as $s)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $s->nisn }}</td>
                                            <td>{{ $s->nama_siswa }}</td>
                                            <td><span
                                                    class="badge bg-label-{{ $s->jenis_kelamin == 'L' ? 'primary' : 'danger' }}">{{ $s->jenis_kelamin }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Belum ada siswa</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
