@extends('layouts.admin')
@section('title', 'Tambah Jenis Pelanggaran')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.jenispelanggaran.index') }}">Data Jenis Pelanggaran</a>
                </li>
                <li class="breadcrumb-item active">Tambah Jenis Pelanggaran</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">Form Tambah Jenis Pelanggaran</h5>
            <div class="card-body">
                <form action="{{ route('admin.jenispelanggaran.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label" for="kategori_pelanggaran">Kategori Pelanggaran <span
                                class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-category"></i></span>
                            <select id="kategori_pelanggaran" name="kategori_pelanggaran"
                                class="form-select @error('kategori_pelanggaran') is-invalid @enderror">
                                <option value="">Pilih Kategori</option>
                                @foreach ($kategoriList as $kat)
                                    <option value="{{ $kat }}"
                                        {{ old('kategori_pelanggaran') == $kat ? 'selected' : '' }}>
                                        {{ str_replace('_', ' ', $kat) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('kategori_pelanggaran')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Pilih kategori pelanggaran</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="nama_pelanggaran">Nama Pelanggaran <span
                                class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-list-ul"></i></span>
                            <textarea id="nama_pelanggaran" name="nama_pelanggaran"
                                class="form-control @error('nama_pelanggaran') is-invalid @enderror" placeholder="Deskripsi pelanggaran..."
                                rows="3" autofocus>{{ old('nama_pelanggaran') }}</textarea>
                        </div>
                        @error('nama_pelanggaran')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="bobot_poin">Bobot Poin <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-star"></i></span>
                            <input type="number" id="bobot_poin" name="bobot_poin"
                                class="form-control @error('bobot_poin') is-invalid @enderror" placeholder="0"
                                value="{{ old('bobot_poin', 0) }}" min="0" step="1" />
                        </div>
                        @error('bobot_poin')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Poin pelanggaran (semakin besar semakin berat)</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.jenispelanggaran.index') }}" class="btn btn-secondary">
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
