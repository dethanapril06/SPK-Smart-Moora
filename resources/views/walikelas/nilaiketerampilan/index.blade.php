@extends('layouts.walikelas')
@section('title', 'Input Nilai Keterampilan')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('walikelas.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Input Nilai Keterampilan</li>
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
                <form action="{{ route('walikelas.nilaiketerampilan.index') }}" method="GET">
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
                        <div class="col-md-5">
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
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bx bx-filter-alt me-1"></i> Tampilkan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if ($filterTA && $filterSemester && $mapelList->count() > 0 && $siswaList->count() > 0)
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0">Daftar Nilai Keterampilan - {{ $kelas->nama_kelas }}</h5>
                        <small class="text-muted">Batas penginputan nilai adalah <strong>0 - 100</strong>.</small>
                    </div>
                    <span class="badge bg-label-primary">{{ $siswaList->count() }} Siswa</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('walikelas.nilaiketerampilan.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_ta" value="{{ $filterTA }}">
                        <input type="hidden" name="id_semester" value="{{ $filterSemester }}">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 50px;">No</th>
                                        <th>Nama Siswa</th>
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
                                            @foreach ($mapelList as $mapel)
                                                @php
                                                    $existing = $siswa->nilaiKeterampilan
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
        @elseif($filterTA && $mapelList->count() == 0)
            <div class="alert alert-warning">
                <i class="bx bx-warning me-1"></i>
                Kelas Anda belum memiliki mata pelajaran yang di-assign. Hubungi Admin untuk mengatur mata pelajaran.
            </div>
        @elseif($filterTA)
            <div class="alert alert-info">Tidak ada siswa ditemukan untuk tahun ajaran yang dipilih.</div>
        @else
            <div class="alert alert-info">Pilih Tahun Ajaran untuk mulai input nilai.</div>
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
