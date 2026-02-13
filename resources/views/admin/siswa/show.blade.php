@extends('layouts.admin')
@section('title', 'Detail Siswa')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.siswa.index') }}">Data Siswa</a>
                </li>
                <li class="breadcrumb-item active">Detail Siswa</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-8">
                <!-- Info Siswa -->
                <div class="card mb-4">
                    <h5 class="card-header">Informasi Siswa</h5>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-semibold" style="width: 200px;">NISN</td>
                                <td>: <strong>{{ $siswa->nisn }}</strong></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Nama Siswa</td>
                                <td>: {{ $siswa->nama_siswa }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Jenis Kelamin</td>
                                <td>:
                                    <span class="badge bg-label-{{ $siswa->jenis_kelamin == 'L' ? 'primary' : 'danger' }}">
                                        {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Kelas</td>
                                <td>:
                                    @if ($siswa->kelas)
                                        <span class="badge bg-label-info">{{ $siswa->kelas->nama_kelas }}</span>
                                    @else
                                        <span class="badge bg-label-secondary">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Tahun Ajaran</td>
                                <td>:
                                    @if ($siswa->tahunAjaran)
                                        {{ $siswa->tahunAjaran->tahun_ajaran }} - {{ $siswa->tahunAjaran->semester }}
                                        @if ($siswa->tahunAjaran->is_active)
                                            <span class="badge bg-label-success">Aktif</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Alamat</td>
                                <td>: {{ $siswa->alamat ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Dibuat Pada</td>
                                <td>: {{ $siswa->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Terakhir Diupdate</td>
                                <td>: {{ $siswa->updated_at->format('d M Y H:i') }}</td>
                            </tr>
                        </table>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i> Kembali
                            </a>
                            <a href="{{ route('admin.siswa.edit', $siswa->id_siswa) }}" class="btn btn-primary">
                                <i class="bx bx-edit me-1"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Statistik -->
                <div class="card mb-3">
                    <h5 class="card-header">Statistik</h5>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-list-check"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">Total Penilaian</small>
                                <h5 class="mb-0">{{ $siswa->penilaian->count() }}</h5>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="bx bx-error"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">Riwayat Pelanggaran</small>
                                <h5 class="mb-0">{{ $siswa->riwayatPelanggaran->count() }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
