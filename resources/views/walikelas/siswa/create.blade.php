@extends('layouts.walikelas')
@section('title', 'Tambah Siswa')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('walikelas.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('walikelas.siswa.index') }}">Data Siswa</a></li>
                <li class="breadcrumb-item active">Tambah Siswa</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">Form Tambah Siswa - {{ $kelas->nama_kelas }}</h5>
            <div class="card-body">
                <form action="{{ route('walikelas.siswa.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="nisn">NISN <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-id-card"></i></span>
                                    <input type="text" class="form-control @error('nisn') is-invalid @enderror"
                                        id="nisn" name="nisn" placeholder="Contoh: 0012345678"
                                        value="{{ old('nisn') }}" autofocus />
                                </div>
                                @error('nisn')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="nama_siswa">Nama Siswa <span
                                        class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-user"></i></span>
                                    <input type="text" id="nama_siswa" name="nama_siswa"
                                        class="form-control @error('nama_siswa') is-invalid @enderror"
                                        placeholder="Nama lengkap siswa" value="{{ old('nama_siswa') }}" />
                                </div>
                                @error('nama_siswa')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="jenis_kelamin">Jenis Kelamin <span
                                        class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-male-female"></i></span>
                                    <select id="jenis_kelamin" name="jenis_kelamin"
                                        class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki
                                        </option>
                                        <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan
                                        </option>
                                    </select>
                                </div>
                                @error('jenis_kelamin')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kelas</label>
                                <input type="text" class="form-control" value="{{ $kelas->nama_kelas }}" disabled />
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="id_ta">Tahun Ajaran <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                            <select id="id_ta" name="id_ta" class="form-select @error('id_ta') is-invalid @enderror">
                                <option value="">Pilih Tahun Ajaran</option>
                                @foreach ($tahunAjaran as $ta)
                                    <option value="{{ $ta->id_ta }}"
                                        {{ old('id_ta') == $ta->id_ta ? 'selected' : '' }}>
                                        {{ $ta->tahun_ajaran }} - {{ $ta->semester }}
                                        {{ $ta->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('id_ta')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="alamat">Alamat</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-map"></i></span>
                            <textarea id="alamat" name="alamat" class="form-control @error('alamat') is-invalid @enderror"
                                placeholder="Alamat lengkap siswa" rows="3">{{ old('alamat') }}</textarea>
                        </div>
                        @error('alamat')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('walikelas.siswa.index') }}" class="btn btn-secondary">
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
