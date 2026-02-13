@extends('layouts.kepalasekolah')
@section('title', 'Penilaian Siswa')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('kepalasekolah.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Penilaian Siswa</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">Penilaian Siswa</h5>
            <div class="card-body">
                <form action="{{ route('kepalasekolah.penilaian.index') }}" method="GET" class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Tahun Ajaran</label>
                        <select class="form-select" name="tahun_ajaran" required>
                            @foreach ($tahunAjaranList as $ta)
                                <option value="{{ $ta->id_ta }}" {{ $filterTA == $ta->id_ta ? 'selected' : '' }}>
                                    {{ $ta->tahun_ajaran }} - {{ $ta->semester }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kelas</label>
                        <select class="form-select" name="kelas">
                            <option value="">Semua Kelas</option>
                            @foreach ($kelasList as $k)
                                <option value="{{ $k->id_kelas }}" {{ $filterKelas == $k->id_kelas ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i> Filter</button>
                        </div>
                    </div>
                </form>
            </div>

            @if ($penilaianList->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="40">No</th>
                                <th>NISN / Nama Siswa</th>
                                <th>Kelas</th>
                                @foreach ($kriteriaList as $k)
                                    <th class="text-center">{{ $k->kode_kriteria }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 0; @endphp
                            @foreach ($penilaianList as $siswaId => $penilaians)
                                @php
                                    $no++;
                                    $siswa = $penilaians->first()->siswa;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $no }}</td>
                                    <td>
                                        <strong>{{ $siswa->nisn }}</strong><br>
                                        <small>{{ $siswa->nama_siswa }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-info">{{ $siswa->kelas->nama_kelas ?? '-' }}</span>
                                    </td>
                                    @foreach ($kriteriaList as $k)
                                        @php
                                            $p = $penilaians->firstWhere('id_kriteria', $k->id_kriteria);
                                        @endphp
                                        <td class="text-center">
                                            @if ($p)
                                                <span title="Asli: {{ $p->nilai_asli }}">{{ $p->nilai_konversi }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <small class="text-muted">Total: {{ $penilaianList->count() }} siswa</small>
                </div>
            @else
                <div class="card-body text-center py-5">
                    <i class="bx bx-star bx-lg text-muted mb-3 d-block"></i>
                    <p class="text-muted">Belum ada data penilaian untuk filter yang dipilih.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
