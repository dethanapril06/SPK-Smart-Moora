@extends('layouts.admin')
@section('title', 'Tambah Mata Pelajaran')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.matapelajaran.index') }}">Data Mata Pelajaran</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">Form Tambah Mata Pelajaran</h5>
            <div class="card-body">
                <form action="{{ route('admin.matapelajaran.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="kode_mapel">Kode Mapel <span
                                        class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-code"></i></span>
                                    <input type="text" class="form-control @error('kode_mapel') is-invalid @enderror"
                                        id="kode_mapel" name="kode_mapel" placeholder="Contoh: MTK"
                                        value="{{ old('kode_mapel') }}" autofocus />
                                </div>
                                @error('kode_mapel')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="nama_mapel">Nama Mata Pelajaran <span
                                        class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-book-open"></i></span>
                                    <input type="text" class="form-control @error('nama_mapel') is-invalid @enderror"
                                        id="nama_mapel" name="nama_mapel" placeholder="Nama mata pelajaran"
                                        value="{{ old('nama_mapel') }}" />
                                </div>
                                @error('nama_mapel')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.matapelajaran.index') }}" class="btn btn-secondary">
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
