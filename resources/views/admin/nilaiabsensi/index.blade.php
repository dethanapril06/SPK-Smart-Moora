@extends('layouts.admin')
@section('title', 'Input Data Absensi')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Input Data Absensi</li>
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
                <form action="{{ route('admin.nilaiabsensi.index') }}" method="GET">
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
                        <div class="col-md-3 mt-3">
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
                        <div class="col-md-1 mt-3 text-end">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="bx bx-filter-alt"></i> Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if ($filterTA && $filterSemester && $filterKelas && $siswaList->count() > 0)
            <div class="card">
                <h5 class="card-header">Input Data Absensi (C6)</h5>
                <div class="card-body">
                    <form action="{{ route('admin.nilaiabsensi.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_ta" value="{{ $filterTA }}">
                        <input type="hidden" name="id_semester" value="{{ $filterSemester }}">
                        <input type="hidden" name="id_kelas" value="{{ $filterKelas }}">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Siswa</th>
                                        <th class="text-center">Sakit</th>
                                        <th class="text-center">Izin</th>
                                        <th class="text-center">Alpa</th>
                                        <th class="text-center">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($siswaList as $siswa)
                                        @php
                                            $existing = $siswa->nilaiAbsensi->first();
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $siswa->nama_siswa }}</td>
                                            <td>
                                                <input type="number"
                                                    class="form-control form-control-sm text-center absensi-input"
                                                    name="jumlah_sakit[{{ $siswa->id_siswa }}]"
                                                    value="{{ $existing ? $existing->jumlah_sakit : 0 }}" min="0"
                                                    data-row="{{ $siswa->id_siswa }}" style="width:80px;">
                                            </td>
                                            <td>
                                                <input type="number"
                                                    class="form-control form-control-sm text-center absensi-input"
                                                    name="jumlah_izin[{{ $siswa->id_siswa }}]"
                                                    value="{{ $existing ? $existing->jumlah_izin : 0 }}" min="0"
                                                    data-row="{{ $siswa->id_siswa }}" style="width:80px;">
                                            </td>
                                            <td>
                                                <input type="number"
                                                    class="form-control form-control-sm text-center absensi-input"
                                                    name="jumlah_alpa[{{ $siswa->id_siswa }}]"
                                                    value="{{ $existing ? $existing->jumlah_alpa : 0 }}" min="0"
                                                    data-row="{{ $siswa->id_siswa }}" style="width:80px;">
                                            </td>
                                            <td class="text-center">
                                                <strong id="total-{{ $siswa->id_siswa }}">
                                                    {{ $existing ? $existing->jumlah_sakit + $existing->jumlah_izin + $existing->jumlah_alpa : 0 }}
                                                </strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan Semua Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @elseif($filterTA && $filterKelas)
            <div class="alert alert-info">Tidak ada siswa ditemukan untuk filter yang dipilih.</div>
        @else
            <div class="alert alert-info">Pilih Tahun Ajaran dan Kelas untuk mulai input data.</div>
        @endif
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('.absensi-input').forEach(input => {
                input.addEventListener('input', function() {
                    const row = this.dataset.row;
                    const inputs = document.querySelectorAll(`.absensi-input[data-row="${row}"]`);
                    let total = 0;
                    inputs.forEach(i => total += parseInt(i.value) || 0);
                    document.getElementById(`total-${row}`).textContent = total;
                });
            });
        </script>
    @endpush
@endsection
