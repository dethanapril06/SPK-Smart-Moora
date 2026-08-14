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
                                <a href="{{ route('admin.nilaiabsensi.index') }}" class="btn btn-outline-secondary ms-1">
                                    <i class="bx bx-reset me-1"></i> Reset
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if ($filterTA && $filterSemester && $siswaList->count() > 0)
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0">Daftar Absensi Siswa</h5>
                        <small class="text-muted">Batas penginputan absensi per kolom maupun total ketidakhadiran adalah <strong>0 - 30 hari</strong>.</small>
                    </div>
                    <span class="badge bg-label-primary">{{ $siswaList->count() }} Siswa</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.nilaiabsensi.store') }}" method="POST" id="form-absensi">
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
                                        <th class="text-center" style="width: 110px;">Sakit (0-30)</th>
                                        <th class="text-center" style="width: 110px;">Izin (0-30)</th>
                                        <th class="text-center" style="width: 110px;">Alpa (0-30)</th>
                                        <th class="text-center" style="width: 100px;">Total (Maks 30)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($siswaList as $siswa)
                                        @php
                                            $existing = $siswa->nilaiAbsensi->first();
                                            $sakit = $existing ? $existing->jumlah_sakit : 0;
                                            $izin = $existing ? $existing->jumlah_izin : 0;
                                            $alpa = $existing ? $existing->jumlah_alpa : 0;
                                            $total = $sakit + $izin + $alpa;
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $siswa->nama_siswa }}</strong><br>
                                                <small class="text-muted">NISN: {{ $siswa->nisn }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-label-info">{{ $siswa->kelas->nama_kelas ?? '-' }}</span>
                                            </td>
                                            <td>
                                                <input type="number"
                                                    class="form-control form-control-sm text-center absensi-input"
                                                    name="jumlah_sakit[{{ $siswa->id_siswa }}]"
                                                    value="{{ $sakit }}" min="0" max="30" step="1"
                                                    data-row="{{ $siswa->id_siswa }}">
                                            </td>
                                            <td>
                                                <input type="number"
                                                    class="form-control form-control-sm text-center absensi-input"
                                                    name="jumlah_izin[{{ $siswa->id_siswa }}]"
                                                    value="{{ $izin }}" min="0" max="30" step="1"
                                                    data-row="{{ $siswa->id_siswa }}">
                                            </td>
                                            <td>
                                                <input type="number"
                                                    class="form-control form-control-sm text-center absensi-input"
                                                    name="jumlah_alpa[{{ $siswa->id_siswa }}]"
                                                    value="{{ $alpa }}" min="0" max="30" step="1"
                                                    data-row="{{ $siswa->id_siswa }}">
                                            </td>
                                            <td class="text-center">
                                                <strong id="total-{{ $siswa->id_siswa }}" class="{{ $total > 30 ? 'text-danger' : 'text-primary' }}">
                                                    {{ $total }}
                                                </strong>
                                                <small class="d-block text-danger err-msg-{{ $siswa->id_siswa }} {{ $total > 30 ? '' : 'd-none' }}">Maks 30!</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 text-end">
                            <button type="submit" class="btn btn-primary" id="btn-submit-absensi">
                                <i class="bx bx-save me-1"></i> Simpan Semua Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @elseif($filterTA && $filterSemester)
            <div class="alert alert-info">Tidak ada siswa ditemukan untuk filter yang dipilih.</div>
        @else
            <div class="alert alert-info">Pilih Tahun Ajaran dan Semester untuk mulai input data.</div>
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                function validateRow(row) {
                    const inputs = document.querySelectorAll(`.absensi-input[data-row="${row}"]`);
                    let total = 0;
                    inputs.forEach(input => {
                        let val = parseInt(input.value);
                        if (isNaN(val) || val < 0) {
                            input.value = 0;
                            val = 0;
                        } else if (val > 30) {
                            input.value = 30;
                            val = 30;
                        }
                        total += val;
                    });

                    const totalElem = document.getElementById(`total-${row}`);
                    const errElem = document.querySelector(`.err-msg-${row}`);
                    if (totalElem) {
                        totalElem.textContent = total;
                        if (total > 30) {
                            totalElem.className = 'text-danger fw-bold';
                            if (errElem) errElem.classList.remove('d-none');
                            inputs.forEach(i => i.classList.add('is-invalid'));
                        } else {
                            totalElem.className = 'text-primary';
                            if (errElem) errElem.classList.add('d-none');
                            inputs.forEach(i => i.classList.remove('is-invalid'));
                        }
                    }
                    return total <= 30;
                }

                document.querySelectorAll('.absensi-input').forEach(input => {
                    input.addEventListener('input', function() {
                        validateRow(this.dataset.row);
                    });
                });

                const form = document.getElementById('form-absensi');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        let isValid = true;
                        document.querySelectorAll('.absensi-input').forEach(input => {
                            const row = input.dataset.row;
                            if (!validateRow(row)) {
                                isValid = false;
                            }
                        });

                        if (!isValid) {
                            e.preventDefault();
                            Swal.fire({
                                icon: 'error',
                                title: 'Batas Absensi Terlampaui',
                                text: 'Total absensi (Sakit + Izin + Alpa) untuk seorang siswa tidak boleh melebihi 30 hari.',
                                confirmButtonText: 'Perbaiki'
                            });
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
