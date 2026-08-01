@extends('layouts.admin')
@section('title', 'Daftar Siswa Lulus')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.siswa.index') }}">Siswa</a>
                    </li>
                    <li class="breadcrumb-item active">Siswa Lulus</li>
                </ol>
            </nav>
            <a href="{{ route('admin.siswa.lulus.pdf', request()->query()) }}" class="btn btn-danger" target="_blank">
                <i class="bx bxs-file-pdf me-1"></i> Export PDF
            </a>
        </div>

        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-header pb-2 d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="bx bx-filter-alt me-1"></i> Filter Data Siswa Lulus</h5>
                @if ($search || $tahunLulus || $jenisKelamin)
                    <a href="{{ route('admin.siswa.lulus') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bx bx-refresh me-1"></i> Reset Filter
                    </a>
                @endif
            </div>
            <div class="card-body">
                <form action="{{ route('admin.siswa.lulus') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label" for="search">Pencarian</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-search"></i></span>
                                <input type="text" class="form-control" id="search" name="search"
                                    placeholder="Cari NISN, nama siswa, atau alamat..."
                                    value="{{ $search }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="tahun_lulus">Tahun Lulus</label>
                            <select class="form-select" id="tahun_lulus" name="tahun_lulus">
                                <option value="">-- Semua Tahun Lulus --</option>
                                @foreach ($tahunLulusList as $tahun)
                                    <option value="{{ $tahun }}" {{ $tahunLulus == $tahun ? 'selected' : '' }}>
                                        {{ $tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
                            <select class="form-select" id="jenis_kelamin" name="jenis_kelamin">
                                <option value="">-- Semua --</option>
                                <option value="L" {{ $jenisKelamin == 'L' ? 'selected' : '' }}>Laki-Laki (L)</option>
                                <option value="P" {{ $jenisKelamin == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bx bx-filter me-1"></i> Terapkan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Siswa Lulus Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Alumni / Siswa Lulus</h5>
                <span class="badge bg-label-success fs-6">
                    Total: {{ $siswa->total() }} Siswa
                </span>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>NISN</th>
                            <th>Nama Siswa</th>
                            <th>JK</th>
                            <th>Tahun Lulus</th>
                            <th>Alamat</th>
                            <th style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($siswa as $item)
                            <tr>
                                <td>{{ ($siswa->currentPage() - 1) * $siswa->perPage() + $loop->iteration }}</td>
                                <td><strong>{{ $item->nisn }}</strong></td>
                                <td>
                                    <div class="fw-bold">{{ $item->nama_siswa }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-label-{{ $item->jenis_kelamin == 'L' ? 'primary' : 'danger' }}">
                                        {{ $item->jenis_kelamin == 'L' ? 'L' : 'P' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-label-success">
                                        <i class="bx bx-check-circle me-1"></i> {{ $item->tahun_lulus ?? '-' }}
                                    </span>
                                </td>
                                <td>{{ $item->alamat ? Str::limit($item->alamat, 40) : '-' }}</td>
                                <td>
                                    <a href="{{ route('admin.siswa.show', $item->id_siswa) }}" class="btn btn-sm btn-icon btn-label-info" data-bs-toggle="tooltip" title="Detail Siswa">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bx bx-info-circle me-1 fs-4 align-middle"></i>
                                    Belum ada data siswa lulus yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($siswa->hasPages())
                <div class="card-footer">
                    {{ $siswa->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection
