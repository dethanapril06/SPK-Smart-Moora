@extends('layouts.admin')
@section('title', 'Input Nilai Sikap')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Input Nilai Sikap</li>
                </ol>
            </nav>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.nilaisikap.index') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <label class="form-label">Tahun Ajaran</label>
                            <select name="tahun_ajaran" class="form-select">
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                @foreach ($tahunAjaranList as $ta)
                                    <option value="{{ $ta->id_ta }}" {{ $filterTA == $ta->id_ta ? 'selected' : '' }}>
                                        {{ $ta->tahun_ajaran }} - Semester {{ $ta->semester }}
                                        {{ $ta->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Kelas</label>
                            <select name="kelas" class="form-select">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelasList as $kelas)
                                    <option value="{{ $kelas->id_kelas }}"
                                        {{ $filterKelas == $kelas->id_kelas ? 'selected' : '' }}>
                                        {{ $kelas->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary"><i class="bx bx-filter-alt"></i> Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if ($filterTA && $filterKelas && $siswaList->count() > 0)
            <div class="card">
                <h5 class="card-header">Input Nilai Sikap (C3)</h5>
                <div class="card-body">
                    <form action="{{ route('admin.nilaisikap.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_ta" value="{{ $filterTA }}">
                        <input type="hidden" name="id_kelas" value="{{ $filterKelas }}">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Siswa</th>
                                        <th class="text-center">Sikap Spiritual</th>
                                        <th class="text-center">Sikap Sosial</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($siswaList as $siswa)
                                        @php
                                            $existing = $siswa->nilaiSikap->first();
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $siswa->nama_siswa }}</td>
                                            <td>
                                                <select class="form-select form-select-sm"
                                                    name="sikap_spiritual[{{ $siswa->id_siswa }}]">
                                                    @foreach (['Sangat Baik', 'Baik', 'Cukup', 'Kurang'] as $opt)
                                                        <option value="{{ $opt }}"
                                                            {{ $existing && $existing->sikap_spiritual == $opt ? 'selected' : '' }}>
                                                            {{ $opt }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-select form-select-sm"
                                                    name="sikap_sosial[{{ $siswa->id_siswa }}]">
                                                    @foreach (['Sangat Baik', 'Baik', 'Cukup', 'Kurang'] as $opt)
                                                        <option value="{{ $opt }}"
                                                            {{ $existing && $existing->sikap_sosial == $opt ? 'selected' : '' }}>
                                                            {{ $opt }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan Semua Nilai
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @elseif($filterTA && $filterKelas)
            <div class="alert alert-info">Tidak ada siswa ditemukan untuk filter yang dipilih.</div>
        @else
            <div class="alert alert-info">Pilih Tahun Ajaran dan Kelas untuk mulai input nilai.</div>
        @endif
    </div>
@endsection
