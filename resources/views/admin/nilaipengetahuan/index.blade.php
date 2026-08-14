@extends('layouts.admin')
@section('title', 'Input Nilai Pengetahuan')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Input Nilai Pengetahuan</li>
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
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.nilaipengetahuan.index') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Tahun Ajaran</label>
                            <select id="tahun_ajaran" name="tahun_ajaran" class="form-select">
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                @foreach ($tahunAjaranList as $ta)
                                    <option value="{{ $ta->id_ta }}" {{ $filterTA == $ta->id_ta ? 'selected' : '' }}>
                                        {{ $ta->tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">Semester</label>
                            <select id="semester" name="semester" class="form-select">
                                <option value="">-- Pilih Semester --</option>
                                @foreach ($semesterList as $s)
                                    <option value="{{ $s->id_semester }}" data-id-ta="{{ $s->id_ta }}" {{ $filterSemester == $s->id_semester ? 'selected' : '' }}>
                                        {{ $s->nama_semester }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">Kelas</label>
                            <select name="kelas" class="form-select">
                                <option value="">-- Semua Kelas --</option>
                                @foreach ($kelasList as $kelas)
                                    <option value="{{ $kelas->id_kelas }}"
                                        {{ $filterKelas == $kelas->id_kelas ? 'selected' : '' }}>
                                        {{ $kelas->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-filter-alt me-1"></i> Tampilkan
                            </button>
                            @if ($filterTA || $filterSemester || $filterKelas)
                                <a href="{{ route('admin.nilaipengetahuan.index') }}" class="btn btn-outline-secondary ms-1">
                                    <i class="bx bx-reset me-1"></i> Reset
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if ($filterTA && $filterSemester && $mapelList->count() > 0 && $siswaList->count() > 0)
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0">Daftar Nilai Pengetahuan</h5>
                        <small class="text-muted">Batas penginputan nilai adalah <strong>0 - 100</strong>.</small>
                    </div>
                    <span class="badge bg-label-primary">{{ $siswaList->count() }} Siswa</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.nilaipengetahuan.store') }}" method="POST" id="form-nilai-pengetahuan">
                        @csrf
                        <input type="hidden" name="id_ta" value="{{ $filterTA }}">
                        <input type="hidden" name="id_semester" value="{{ $filterSemester }}">
                        <input type="hidden" name="id_kelas" value="{{ $filterKelas }}">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 50px;">No</th>
                                        <th>Nama Siswa</th>
                                        <th class="text-center" style="width: 120px;">Kelas</th>
                                        @foreach ($mapelList as $mapel)
                                            <th class="text-center" style="min-width:90px;">{{ $mapel->kode_mapel }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($siswaList as $siswa)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $siswa->nama_siswa }}</strong><br>
                                                <small class="text-muted">NISN: {{ $siswa->nisn }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-label-info">{{ $siswa->kelas->nama_kelas ?? '-' }}</span>
                                            </td>
                                            @foreach ($mapelList as $mapel)
                                                @php
                                                    $existing = $siswa->nilaiPengetahuan
                                                        ->where('id_mapel', $mapel->id_mapel)
                                                        ->first();
                                                @endphp
                                                <td>
                                                    <input type="number" class="form-control form-control-sm text-center input-nilai"
                                                        name="nilai[{{ $siswa->id_siswa }}][{{ $mapel->id_mapel }}]"
                                                        value="{{ $existing ? $existing->nilai : '' }}" min="0"
                                                        max="100" step="0.01" style="min-width:80px;"
                                                        placeholder="0-100">
                                                </td>
                                            @endforeach
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
        @elseif($filterTA && $filterSemester && $mapelList->count() == 0)
            <div class="alert alert-warning">
                <i class="bx bx-warning me-1"></i>
                Belum ada mata pelajaran yang terdaftar.
                <a href="{{ route('admin.matapelajaran.index') }}">Atur mata pelajaran</a> terlebih dahulu.
            </div>
        @elseif($filterTA && $filterSemester)
            <div class="alert alert-info">Tidak ada siswa ditemukan untuk filter yang dipilih.</div>
        @else
            <div class="alert alert-info">Pilih Tahun Ajaran dan Semester untuk mulai input nilai.</div>
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.input-nilai').forEach(input => {
                    input.addEventListener('input', function() {
                        if (this.value !== '') {
                            let val = parseFloat(this.value);
                            if (val < 0) {
                                this.value = 0;
                            } else if (val > 100) {
                                this.value = 100;
                            }
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
