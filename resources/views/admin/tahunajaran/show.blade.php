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
                                <td class="fw-semibold">Semester</td>
                                <td>:
                                    @forelse ($tahunajaran->semesters as $semester)
                                        <span
                                            class="badge bg-label-{{ $semester->nama_semester == 'Ganjil' ? 'primary' : 'info' }} me-1">
                                            {{ $semester->nama_semester }}{{ $semester->is_active ? ' (Aktif)' : '' }}
                                        </span>
                                    @empty
                                        <span class="badge bg-label-secondary">Belum Ada Semester</span>
                                    @endforelse
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Status</td>
                                <td>:
                                    @if ($tahunajaran->is_active)
                                        <span class="badge bg-label-success">Aktif</span>
                                    @else
                                        <span class="badge bg-label-secondary">Tidak Aktif</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Jumlah Siswa</td>
                                <td>: <span class="badge bg-label-primary">{{ $tahunajaran->siswa->count() }} siswa</span>
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
                                            <i class="bx bx-check-circle me-1"></i> Aktifkan
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
                                                {{ $semester->nama_semester }}
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
                                <h5 class="mb-0">{{ $tahunajaran->siswa->count() }}</h5>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="bx bx-list-check"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">Total Penilaian</small>
                                <h5 class="mb-0">{{ $tahunajaran->penilaian->count() }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
