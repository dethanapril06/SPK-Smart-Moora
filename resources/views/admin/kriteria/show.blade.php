@extends('layouts.admin')
@section('title', 'Detail Kriteria')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.kriteria.index') }}">Data Kriteria</a>
                </li>
                <li class="breadcrumb-item active">Detail Kriteria</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-8">
                <!-- Info Kriteria -->
                <div class="card mb-4">
                    <h5 class="card-header">Informasi Kriteria</h5>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-semibold" style="width: 200px;">Kode Kriteria</td>
                                <td>: <strong>{{ $kriterium->kode_kriteria }}</strong></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Nama Kriteria</td>
                                <td>: {{ $kriterium->nama_kriteria }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Jenis Kriteria</td>
                                <td>:
                                    <span
                                        class="badge bg-label-{{ $kriterium->jenis_kriteria == 'Benefit' ? 'success' : 'warning' }}">
                                        {{ $kriterium->jenis_kriteria }}
                                    </span>
                                    @if ($kriterium->jenis_kriteria == 'Benefit')
                                        <small class="text-muted">(Semakin tinggi semakin baik)</small>
                                    @else
                                        <small class="text-muted">(Semakin rendah semakin baik)</small>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Bobot</td>
                                <td>: <span class="badge bg-label-primary">{{ $kriterium->bobot }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Jumlah Sub Kriteria</td>
                                <td>: {{ $kriterium->subKriteria->count() }} sub kriteria</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Dibuat Pada</td>
                                <td>: {{ $kriterium->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Terakhir Diupdate</td>
                                <td>: {{ $kriterium->updated_at->format('d M Y H:i') }}</td>
                            </tr>
                        </table>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.kriteria.index') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i> Kembali
                            </a>
                            <a href="{{ route('admin.kriteria.edit', $kriterium->id_kriteria) }}" class="btn btn-primary">
                                <i class="bx bx-edit me-1"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Sub Kriteria List -->
                @if ($kriterium->subKriteria->count() > 0)
                    <div class="card">
                        <h5 class="card-header">Daftar Sub Kriteria</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Sub Kriteria</th>
                                        <th>Rentang Nilai</th>
                                        <th>Bobot</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kriterium->subKriteria as $sub)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $sub->nama_subkriteria }}</td>
                                            <td><span class="badge bg-label-info">{{ $sub->nilai_awal }} -
                                                    {{ $sub->nilai_akhir }}</span></td>
                                            <td><span class="badge bg-label-success">{{ $sub->bobot_subkriteria }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-md-4">
                <!-- Statistik -->
                <div class="card">
                    <h5 class="card-header">Statistik</h5>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-list-ul"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">Total Sub Kriteria</small>
                                <h5 class="mb-0">{{ $kriterium->subKriteria->count() }}</h5>
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
                                <h5 class="mb-0">{{ $kriterium->penilaian->count() }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
