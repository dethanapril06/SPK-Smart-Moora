@extends('layouts.admin')
@section('title', 'Tambah Kriteria')
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
                <li class="breadcrumb-item active">Tambah Kriteria</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">Form Tambah Kriteria</h5>
            <div class="card-body">
                <form action="{{ route('admin.kriteria.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="kode_kriteria">Kode Kriteria <span
                                        class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-code"></i></span>
                                    <input type="text" class="form-control @error('kode_kriteria') is-invalid @enderror"
                                        id="kode_kriteria" name="kode_kriteria" placeholder="Contoh: C1"
                                        value="{{ old('kode_kriteria') }}" autofocus />
                                </div>
                                @error('kode_kriteria')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Kode unik untuk kriteria (misal: C1, C2, dst)</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="nama_kriteria">Nama Kriteria <span
                                        class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-list-ul"></i></span>
                                    <input type="text" id="nama_kriteria" name="nama_kriteria"
                                        class="form-control @error('nama_kriteria') is-invalid @enderror"
                                        placeholder="Nama kriteria" value="{{ old('nama_kriteria') }}" />
                                </div>
                                @error('nama_kriteria')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="jenis_kriteria">Jenis Kriteria <span
                                        class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-category"></i></span>
                                    <select id="jenis_kriteria" name="jenis_kriteria"
                                        class="form-select @error('jenis_kriteria') is-invalid @enderror">
                                        <option value="">Pilih Jenis Kriteria</option>
                                        <option value="Benefit" {{ old('jenis_kriteria') == 'Benefit' ? 'selected' : '' }}>
                                            Benefit (Semakin Tinggi Semakin Baik)</option>
                                        <option value="Cost" {{ old('jenis_kriteria') == 'Cost' ? 'selected' : '' }}>Cost
                                            (Semakin Rendah Semakin Baik)</option>
                                    </select>
                                </div>
                                @error('jenis_kriteria')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <strong>Benefit:</strong> nilai tinggi = baik (misal: Nilai ujian)<br>
                                    <strong>Cost:</strong> nilai rendah = baik (misal: Jumlah pelanggaran)
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="bobot">Bobot <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-calculator"></i></span>
                                    <input type="number" id="bobot" name="bobot"
                                        class="form-control @error('bobot') is-invalid @enderror" placeholder="0"
                                        value="{{ old('bobot', 0) }}" min="0" max="100" step="0.01" />
                                </div>
                                @error('bobot')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Bobot kepentingan kriteria (0-100)</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.kriteria.index') }}" class="btn btn-secondary">
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
