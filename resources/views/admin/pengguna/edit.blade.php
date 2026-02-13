@extends('layouts.admin')
@section('title', 'Edit Pengguna')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style2 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.pengguna.index') }}">Data Pengguna</a>
                </li>
                <li class="breadcrumb-item active">Edit Pengguna</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">Form Edit Pengguna</h5>
            <div class="card-body">
                <form action="{{ route('admin.pengguna.update', $pengguna->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label" for="name">Nama Lengkap <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-user"></i></span>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" placeholder="Masukkan nama lengkap"
                                value="{{ old('name', $pengguna->name) }}" autofocus />
                        </div>
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                            <input type="email" id="email" name="email"
                                class="form-control @error('email') is-invalid @enderror" placeholder="pengguna@example.com"
                                value="{{ old('email', $pengguna->email) }}" />
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="level">Level <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-shield"></i></span>
                            <select id="level" name="level" class="form-select @error('level') is-invalid @enderror">
                                <option value="">Pilih Level</option>
                                <option value="Admin" {{ old('level', $pengguna->level) == 'Admin' ? 'selected' : '' }}>
                                    Admin</option>
                                <option value="Wali Kelas"
                                    {{ old('level', $pengguna->level) == 'Wali Kelas' ? 'selected' : '' }}>Wali Kelas
                                </option>
                                <option value="Kepala Sekolah"
                                    {{ old('level', $pengguna->level) == 'Kepala Sekolah' ? 'selected' : '' }}>Kepala
                                    Sekolah</option>
                            </select>
                        </div>
                        @error('level')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-warning">
                        <i class="bx bx-info-circle me-1"></i>
                        Password tidak akan berubah. Gunakan tombol "Reset Password" di halaman daftar pengguna jika ingin
                        mereset password.
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.pengguna.index') }}" class="btn btn-secondary">
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
