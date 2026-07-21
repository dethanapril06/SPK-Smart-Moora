@extends('layouts.walikelas')
@section('title', 'Input Nilai Ekstrakurikuler')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('walikelas.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Input Nilai Ekstrakurikuler</li>
                </ol>
            </nav>
            @if ($filterTA && $filterSemester)
                <a href="{{ route('walikelas.nilaiekstrakurikuler.create', ['tahun_ajaran' => $filterTA, 'semester' => $filterSemester]) }}"
                    class="btn btn-sm btn-primary">
                    <i class="bx bx-plus me-1"></i>
                </a>
            @endif
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
                <form action="{{ route('walikelas.nilaiekstrakurikuler.index') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-5">
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
                        <div class="col-md-5 mt-3">
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
                        <div class="col-md-2 mt-3 text-end">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="bx bx-filter-alt"></i> Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if ($filterTA && $filterSemester && $siswaList->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Data Nilai Ekstrakurikuler - {{ $kelas->nama_kelas }}</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Nama Siswa</th>
                                <th>NISN</th>
                                <th>Ekstrakurikuler</th>
                                <th>Predikat</th>
                                <th style="width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($siswaList as $siswa)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $siswa->nama_siswa }}</td>
                                    <td>{{ $siswa->nisn }}</td>
                                    <td>
                                        @forelse($siswa->nilaiEkstrakurikuler as $idx => $ekskul)
                                            <div style="line-height: 2;">
                                                @if ($siswa->nilaiEkstrakurikuler->count() > 1)
                                                    {{ $idx + 1 }}.
                                                @endif
                                                {{ $ekskul->nama_ekskul }}
                                            </div>
                                        @empty
                                            <span class="text-muted fst-italic">Belum ada</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        @forelse($siswa->nilaiEkstrakurikuler as $idx => $ekskul)
                                            <div style="line-height: 2;">
                                                @if ($siswa->nilaiEkstrakurikuler->count() > 1)
                                                    {{ $idx + 1 }}.
                                                @endif
                                                <span
                                                    class="badge bg-label-{{ $ekskul->predikat == 'Sangat Baik' ? 'success' : ($ekskul->predikat == 'Baik' ? 'primary' : ($ekskul->predikat == 'Cukup' ? 'warning' : 'danger')) }}">
                                                    {{ $ekskul->predikat }}
                                                </span>
                                            </div>
                                        @empty
                                            <span class="text-muted">-</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        @if ($siswa->nilaiEkstrakurikuler->count() > 0)
                                            <a href="{{ route('walikelas.nilaiekstrakurikuler.edit', [
                                                'id' => $siswa->id_siswa,
                                                'tahun_ajaran' => $filterTA,
                                            ]) }}"
                                                class="btn btn-sm btn-icon btn-label-warning"
                                                title="Kelola ekstrakurikuler {{ $siswa->nama_siswa }}">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @elseif($filterTA)
            <div class="alert alert-info">Tidak ada siswa ditemukan untuk tahun ajaran yang dipilih.</div>
        @else
            <div class="alert alert-info">Pilih Tahun Ajaran untuk mulai input nilai.</div>
        @endif
    </div>
@endsection
