@extends('layouts.admin')
@section('title', 'Tambah Tahun Ajaran')
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
                <li class="breadcrumb-item active">Tambah Tahun Ajaran</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">Form Tambah Tahun Ajaran</h5>
            <div class="card-body">
                <form action="{{ route('admin.tahunajaran.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label" for="tahun_ajaran">Tahun Ajaran <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                            <input type="text" class="form-control @error('tahun_ajaran') is-invalid @enderror"
                                id="tahun_ajaran" name="tahun_ajaran" placeholder="Contoh: 2024/2025"
                                value="{{ old('tahun_ajaran') }}" autofocus />
                        </div>
                        @error('tahun_ajaran')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Semester akan dibuat otomatis: Ganjil dan Genap.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                {{ old('is_active') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Aktifkan tahun ajaran ini
                            </label>
                        </div>
                        <div class="form-text">Jika diaktifkan, tahun ajaran lain akan dinonaktifkan</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.tahunajaran.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
