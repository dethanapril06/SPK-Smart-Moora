@extends('layouts.admin')
@section('title', 'Edit Kelas')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.kelas.index') }}">Data Kelas</a>
                </li>
                <li class="breadcrumb-item active">Edit Kelas</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span>Form Edit Kelas</span>
                <span class="badge {{ $jumlahSiswa > 0 ? 'bg-label-primary' : 'bg-label-success' }} fs-6">
                    <i class="bx bx-user me-1"></i> Terisi {{ $jumlahSiswa }} Siswa
                </span>
            </h5>
            <div class="card-body">
                @if ($jumlahSiswa > 0)
                    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                        <i class="bx bx-error-circle fs-4 me-2"></i>
                        <div>
                            <strong>Perhatian!</strong> Kelas ini memiliki <strong>{{ $jumlahSiswa }} siswa</strong> yang terdaftar. Kapasitas kelas hanya dapat <strong>ditambah</strong> atau dipertahankan (minimal <strong>{{ $jumlahSiswa }}</strong>) agar siswa yang ada tidak menjadi tidak muat.
                        </div>
                    </div>
                @else
                    <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                        <i class="bx bx-check-circle fs-4 me-2"></i>
                        <div>
                            <strong>Kelas Kosong:</strong> Kelas ini belum memiliki siswa (0 siswa). Anda dapat mengubah kuota/kapasitas kelas ini secara bebas.
                        </div>
                    </div>
                @endif

                <form action="{{ route('admin.kelas.update', $kela->id_kelas) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label" for="id_kelas">ID Kelas</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-id-card"></i></span>
                            <input type="text" class="form-control" id="id_kelas" value="{{ $kela->id_kelas }}"
                                disabled />
                        </div>
                        <div class="form-text">ID kelas tidak dapat diubah</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="nama_kelas">Nama Kelas <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-book"></i></span>
                            <input type="text" id="nama_kelas" name="nama_kelas"
                                class="form-control @error('nama_kelas') is-invalid @enderror"
                                placeholder="Contoh: Kelas X IPA 1" value="{{ old('nama_kelas', $kela->nama_kelas) }}"
                                autofocus />
                        </div>
                        @error('nama_kelas')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="id_wali_kelas">Wali Kelas</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-user"></i></span>
                            <select id="id_wali_kelas" name="id_wali_kelas"
                                class="form-select @error('id_wali_kelas') is-invalid @enderror">
                                <option value="">Pilih Wali Kelas</option>
                                @foreach ($waliKelas as $wali)
                                    <option value="{{ $wali->id }}"
                                        {{ old('id_wali_kelas', $kela->id_wali_kelas) == $wali->id ? 'selected' : '' }}>
                                        {{ $wali->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('id_wali_kelas')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="kapasitas">Kapasitas / Kuota Kelas <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-group"></i></span>
                            <input type="number" id="kapasitas" name="kapasitas"
                                class="form-control @error('kapasitas') is-invalid @enderror" placeholder="0"
                                value="{{ old('kapasitas', $kela->kapasitas) }}" min="{{ $jumlahSiswa }}" />
                        </div>
                        @error('kapasitas')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            @if ($jumlahSiswa > 0)
                                <span class="text-warning fw-semibold"><i class="bx bx-info-circle me-1"></i> Minimal {{ $jumlahSiswa }} siswa karena kelas saat ini terisi {{ $jumlahSiswa }} siswa.</span>
                            @else
                                Maksimal jumlah siswa yang dapat diterima di kelas ini.
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mata Pelajaran</label>
                        <div class="alert alert-info mb-0" role="alert">
                            <i class="bx bx-info-circle me-1"></i>
                            Semua mata pelajaran otomatis tersedia untuk kelas ini.
                            <span class="fw-semibold">{{ $mataPelajaran->count() }}</span> mapel aktif akan digunakan.
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Kembali
                        </a>
                        <button class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
