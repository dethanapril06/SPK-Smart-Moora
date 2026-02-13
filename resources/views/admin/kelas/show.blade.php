@extends('layouts.admin')
@section('title', 'Detail Kelas')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.kelas.index') }}">Data Kelas</a>
                </li>
                <li class="breadcrumb-item active">Detail Kelas</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-8">
                <!-- Info Kelas -->
                <div class="card mb-4">
                    <h5 class="card-header">Informasi Kelas</h5>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-semibold" style="width: 200px;">ID Kelas</td>
                                <td>: <strong>{{ $kela->id_kelas }}</strong></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Nama Kelas</td>
                                <td>: {{ $kela->nama_kelas }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Wali Kelas</td>
                                <td>:
                                    @if ($kela->waliKelas)
                                        <span class="badge bg-label-info">{{ $kela->waliKelas->name }}</span>
                                    @else
                                        <span class="badge bg-label-secondary">Belum Ada</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Jumlah Siswa</td>
                                <td>: <span class="badge bg-label-primary">{{ $kela->siswa->count() }} siswa</span></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Kapasitas</td>
                                <td>: {{ $kela->kapasitas ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Dibuat Pada</td>
                                <td>: {{ $kela->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Terakhir Diupdate</td>
                                <td>: {{ $kela->updated_at->format('d M Y H:i') }}</td>
                            </tr>
                        </table>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i> Kembali
                            </a>
                            <a href="{{ route('admin.kelas.edit', $kela->id_kelas) }}" class="btn btn-primary">
                                <i class="bx bx-edit me-1"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Statistik -->
                <div class="card">
                    <h5 class="card-header">Statistik</h5>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-group"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">Total Siswa</small>
                                <h5 class="mb-0">{{ $kela->siswa->count() }}</h5>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="bx bx-user"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">Wali Kelas</small>
                                <h6 class="mb-0">{{ $kela->waliKelas->name ?? '-' }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
