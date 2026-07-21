@extends('layouts.walikelas')
@section('title', 'Kelola Nilai Ekstrakurikuler')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('walikelas.dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('walikelas.nilaiekstrakurikuler.index', [
                        'tahun_ajaran' => $selectedTA,
                    ]) }}">Nilai Ekstrakurikuler</a>
                </li>
                <li class="breadcrumb-item active">Kelola</li>
            </ol>
        </nav>

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

        <div class="card mb-4">
            <h5 class="card-header">Data Siswa</h5>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tahun Ajaran</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                            <input type="text" class="form-control"
                                value="{{ $tahunAjaran->tahun_ajaran }} - Semester {{ $tahunAjaran->semester }}"
                                readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kelas</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-book"></i></span>
                            <input type="text" class="form-control" value="{{ optional($siswa->kelas)->nama_kelas ?? '-' }}"
                                readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label">Nama Siswa</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-user"></i></span>
                            <input type="text" class="form-control" value="{{ $siswa->nama_siswa }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">NISN</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-id-card"></i></span>
                            <input type="text" class="form-control" value="{{ $siswa->nisn }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <h5 class="card-header">Ekstrakurikuler Terdaftar</h5>
            <div class="card-body">
                @if ($ekskulList->count() > 0)
                    <form action="{{ route('walikelas.nilaiekstrakurikuler.updateAll', $siswa->id_siswa) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id_ta" value="{{ $selectedTA }}">
                        <input type="hidden" name="id_semester" value="{{ $selectedSemester }}">

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Nama Ekstrakurikuler</th>
                                        <th style="width: 220px;">Predikat</th>
                                        <th style="width: 80px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ekskulList as $ekskul)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <input type="text"
                                                    class="form-control @error('ekskul.'.$ekskul->id_nilai_ekskul.'.nama_ekskul') is-invalid @enderror"
                                                    name="ekskul[{{ $ekskul->id_nilai_ekskul }}][nama_ekskul]"
                                                    value="{{ old('ekskul.'.$ekskul->id_nilai_ekskul.'.nama_ekskul', $ekskul->nama_ekskul) }}" required>
                                                @error('ekskul.'.$ekskul->id_nilai_ekskul.'.nama_ekskul')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <select class="form-select @error('ekskul.'.$ekskul->id_nilai_ekskul.'.predikat') is-invalid @enderror"
                                                    name="ekskul[{{ $ekskul->id_nilai_ekskul }}][predikat]"
                                                    required>
                                                    @foreach (['Sangat Baik', 'Baik', 'Cukup', 'Kurang'] as $predikat)
                                                        <option value="{{ $predikat }}"
                                                            {{ old('ekskul.'.$ekskul->id_nilai_ekskul.'.predikat', $ekskul->predikat) == $predikat ? 'selected' : '' }}>
                                                            {{ $predikat }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('ekskul.'.$ekskul->id_nilai_ekskul.'.predikat')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <button type="submit" class="btn btn-sm btn-icon btn-label-danger mb-1"
                                                    form="delete-ekskul-{{ $ekskul->id_nilai_ekskul }}"
                                                    onclick="return confirm('Yakin hapus {{ $ekskul->nama_ekskul }}?')"
                                                    title="Hapus {{ $ekskul->nama_ekskul }}">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3 text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan Semua
                            </button>
                        </div>
                    </form>

                    {{-- Hidden delete forms defined outside the update form to avoid nested forms --}}
                    @foreach ($ekskulList as $ekskul)
                        <form id="delete-ekskul-{{ $ekskul->id_nilai_ekskul }}"
                            action="{{ route('walikelas.nilaiekstrakurikuler.destroy', $ekskul->id_nilai_ekskul) }}"
                            method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endforeach
                @else
                    <div class="alert alert-info mb-0">Belum ada ekstrakurikuler untuk siswa ini.</div>
                @endif
            </div>
        </div>

        <div class="card">
            <h5 class="card-header">Tambah Ekstrakurikuler Baru</h5>
            <div class="card-body">
                <form action="{{ route('walikelas.nilaiekstrakurikuler.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_ta" value="{{ $selectedTA }}">
                    <input type="hidden" name="id_semester" value="{{ $selectedSemester }}">
                    <input type="hidden" name="id_siswa" value="{{ $siswa->id_siswa }}">
                    <input type="hidden" name="redirect_to_edit" value="1">

                    <div class="row align-items-end">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label">Nama Ekstrakurikuler <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('ekskul.0.nama_ekskul') is-invalid @enderror"
                                name="ekskul[0][nama_ekskul]" placeholder="Contoh: Pramuka"
                                value="{{ old('ekskul.0.nama_ekskul') }}" required>
                            @error('ekskul.0.nama_ekskul')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="form-label">Predikat <span class="text-danger">*</span></label>
                            <select class="form-select @error('ekskul.0.predikat') is-invalid @enderror"
                                name="ekskul[0][predikat]" required>
                                @foreach (['Sangat Baik', 'Baik', 'Cukup', 'Kurang'] as $predikat)
                                    <option value="{{ $predikat }}"
                                        {{ old('ekskul.0.predikat', 'Baik') == $predikat ? 'selected' : '' }}>
                                        {{ $predikat }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ekskul.0.predikat')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2 text-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan
                            </button>
                        </div>
                    </div>
                </form>

                <hr class="my-4">

                <a href="{{ route('walikelas.nilaiekstrakurikuler.index', [
                    'tahun_ajaran' => $selectedTA,
                ]) }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
@endsection
