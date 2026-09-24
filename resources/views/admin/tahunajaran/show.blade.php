@extends('layouts.admin')
@section('title', 'Detail Tahun Ajaran')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.tahunajaran.index') }}">Data Tahun Ajaran</a>
                </li>
                <li class="breadcrumb-item active">Detail Tahun Ajaran</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-8">
                <!-- Info Tahun Ajaran -->
                <div class="card mb-4">
                    <h5 class="card-header">Informasi Tahun Ajaran</h5>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-semibold" style="width: 200px;">Tahun Ajaran</td>
                                <td>: <strong>{{ $tahunajaran->tahun_ajaran }}</strong></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Semester & Periode</td>
                                <td>:
                                    @forelse ($tahunajaran->semesters as $semester)
                                        <div class="mb-1">
                                            <span
                                                class="badge bg-label-{{ $semester->nama_semester == 'Ganjil' ? 'primary' : 'info' }}">
                                                {{ $semester->nama_semester }} ({{ $semester->periode_bulan }})
                                                @if ($semester->is_active)
                                                    <i class="bx bx-check ms-1"></i> (Sedang Aktif)
                                                @endif
                                            </span>
                                        </div>
                                    @empty
                                        <span class="badge bg-label-secondary">Belum Ada Semester</span>
                                    @endforelse
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Status Tahun Ajaran</td>
                                <td>:
                                    @if ($tahunajaran->is_active)
                                        <span class="badge bg-label-success">Aktif</span>
                                    @else
                                        <span class="badge bg-label-secondary">Tidak Aktif</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Rincian Siswa Semester Ganjil</td>
                                <td>:
                                    <span class="badge bg-label-primary">{{ $tahunajaran->countSiswaSemester('Ganjil') }} siswa</span>
                                    <small class="text-muted ms-1">(Periode Juli - Desember)</small>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Rincian Siswa Semester Genap</td>
                                <td>:
                                    <span class="badge bg-label-info">{{ $tahunajaran->countSiswaSemester('Genap') }} siswa</span>
                                    <small class="text-muted ms-1">(Periode Januari - Juni)</small>
                                    @if ($tahunajaran->countSiswaBaruGenap() > 0)
                                        <div class="mt-1">
                                            <span class="badge bg-label-success">
                                                <i class="bx bx-user-plus me-1"></i>+{{ $tahunajaran->countSiswaBaruGenap() }} Siswa Baru/Pindahan di Genap
                                            </span>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Jumlah Penilaian</td>
                                <td>: {{ $tahunajaran->penilaian->count() }} data</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Dibuat Pada</td>
                                <td>: {{ $tahunajaran->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Terakhir Diupdate</td>
                                <td>: {{ $tahunajaran->updated_at->format('d M Y H:i') }}</td>
                            </tr>
                        </table>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.tahunajaran.index') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i> Kembali
                            </a>
                            <div>
                                @if (!$tahunajaran->is_active)
                                    <form action="{{ route('admin.tahunajaran.set-active', $tahunajaran->id_ta) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-success me-2">
                                            <i class="bx bx-check-circle me-1"></i> Aktifkan TA
                                        </button>
                                    </form>
                                @endif
                                @foreach ($tahunajaran->semesters as $semester)
                                    @if (!$semester->is_active)
                                        <form
                                            action="{{ route('admin.tahunajaran.set-active-semester', [$tahunajaran->id_ta, $semester->id_semester]) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-outline-info me-2">
                                                <i class="bx bx-time-five me-1"></i> Aktifkan
                                                {{ $semester->nama_semester }} ({{ $semester->periode_bulan }})
                                            </button>
                                        </form>
                                    @endif
                                @endforeach
                                <a href="{{ route('admin.tahunajaran.edit', $tahunajaran->id_ta) }}"
                                    class="btn btn-primary">
                                    <i class="bx bx-edit me-1"></i> Edit
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Statistik -->
                <div class="card">
                    <h5 class="card-header">Statistik Siswa & Penilaian</h5>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-group"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">Siswa Semester Ganjil</small>
                                <h5 class="mb-0">{{ $tahunajaran->countSiswaSemester('Ganjil') }} siswa</h5>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="bx bx-group"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">Siswa Semester Genap</small>
                                <h5 class="mb-0">{{ $tahunajaran->countSiswaSemester('Genap') }} siswa</h5>
                                @if ($tahunajaran->countSiswaBaruGenap() > 0)
                                    <small class="text-success">+{{ $tahunajaran->countSiswaBaruGenap() }} siswa baru</small>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-secondary">
                                    <i class="bx bx-list-check"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">Total Penilaian</small>
                                <h5 class="mb-0">{{ $tahunajaran->penilaian->count() }} data</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
