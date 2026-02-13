@extends('layouts.admin')
@section('title', 'Edit Sub Kriteria')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.subkriteria.index') }}">Data Sub Kriteria</a>
                </li>
                <li class="breadcrumb-item active">Edit Sub Kriteria</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">Form Edit Sub Kriteria</h5>
            <div class="card-body">
                <form action="{{ route('admin.subkriteria.update', $subkriterium->id_subkriteria) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label" for="id_kriteria">Kriteria <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-slider"></i></span>
                            <select id="id_kriteria" name="id_kriteria"
                                class="form-select @error('id_kriteria') is-invalid @enderror">
                                <option value="">Pilih Kriteria</option>
                                @foreach ($kriteria as $k)
                                    <option value="{{ $k->id_kriteria }}"
                                        {{ old('id_kriteria', $subkriterium->id_kriteria) == $k->id_kriteria ? 'selected' : '' }}>
                                        {{ $k->kode_kriteria }} - {{ $k->nama_kriteria }} ({{ $k->jenis_kriteria }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('id_kriteria')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="nama_subkriteria">Nama Sub Kriteria <span
                                class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-list-ul"></i></span>
                            <input type="text" id="nama_subkriteria" name="nama_subkriteria"
                                class="form-control @error('nama_subkriteria') is-invalid @enderror"
                                placeholder="Contoh: Sangat Baik"
                                value="{{ old('nama_subkriteria', $subkriterium->nama_subkriteria) }}" autofocus />
                        </div>
                        @error('nama_subkriteria')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="nilai_awal">Nilai Awal <span
                                        class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-trending-down"></i></span>
                                    <input type="number" id="nilai_awal" name="nilai_awal"
                                        class="form-control @error('nilai_awal') is-invalid @enderror" placeholder="0"
                                        value="{{ old('nilai_awal', $subkriterium->nilai_awal) }}" min="0"
                                        step="0.01" />
                                </div>
                                @error('nilai_awal')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Batas bawah rentang nilai</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="nilai_akhir">Nilai Akhir <span
                                        class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-trending-up"></i></span>
                                    <input type="number" id="nilai_akhir" name="nilai_akhir"
                                        class="form-control @error('nilai_akhir') is-invalid @enderror" placeholder="0"
                                        value="{{ old('nilai_akhir', $subkriterium->nilai_akhir) }}" min="0"
                                        step="0.01" />
                                </div>
                                @error('nilai_akhir')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Batas atas rentang nilai</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="bobot_subkriteria">Bobot Sub Kriteria <span
                                class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-calculator"></i></span>
                            <input type="number" id="bobot_subkriteria" name="bobot_subkriteria"
                                class="form-control @error('bobot_subkriteria') is-invalid @enderror" placeholder="0"
                                value="{{ old('bobot_subkriteria', $subkriterium->bobot_subkriteria) }}" min="0"
                                step="0.01" />
                        </div>
                        @error('bobot_subkriteria')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Bobot/skor untuk sub kriteria ini</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.subkriteria.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
