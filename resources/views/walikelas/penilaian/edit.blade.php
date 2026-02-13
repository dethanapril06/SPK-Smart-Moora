@extends('layouts.walikelas')
@section('title', 'Edit Penilaian Siswa')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('walikelas.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('walikelas.penilaian.index') }}">Penilaian Siswa</a></li>
                <li class="breadcrumb-item active">Edit Penilaian</li>
            </ol>
        </nav>

        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Siswa:</strong> {{ $siswa->nama_siswa }} ({{ $siswa->nisn }})<br>
                        <strong>Kelas:</strong> {{ $siswa->kelas ? $siswa->kelas->nama_kelas : '-' }}
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="filter_ta">Tahun Ajaran</label>
                        <select id="filter_ta" class="form-select"
                            onchange="window.location.href='{{ route('walikelas.penilaian.edit', $siswa->id_siswa) }}?ta=' + this.value">
                            @foreach ($tahunAjaranList as $ta)
                                <option value="{{ $ta->id_ta }}" {{ $filterTA == $ta->id_ta ? 'selected' : '' }}>
                                    {{ $ta->tahun_ajaran }} - {{ $ta->semester }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <h5 class="card-header">Form Edit Penilaian</h5>
            <div class="card-body">
                <form action="{{ route('walikelas.penilaian.update', $siswa->id_siswa) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id_ta" value="{{ $filterTA }}">

                    @foreach ($kriteriaList as $kriteria)
                        @php
                            $penilaian = $penilaianList[$kriteria->id_kriteria] ?? null;
                        @endphp
                        <div class="card mb-3 kriteria-section">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-0">
                                            {{ $kriteria->kode_kriteria }} - {{ $kriteria->nama_kriteria }}
                                            @if ($kriteria->kode_kriteria == 'C5')
                                                <span class="badge bg-label-warning ms-2"><i class="bx bx-bolt"></i>
                                                    Otomatis</span>
                                            @endif
                                        </h6>
                                        <small class="text-muted">Jenis: <strong>{{ $kriteria->jenis_kriteria }}</strong> |
                                            Bobot: <strong>{{ $kriteria->bobot }}</strong></small>
                                    </div>
                                </div>

                                @if ($kriteria->kode_kriteria == 'C5')
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">Total Poin Pelanggaran</label>
                                            <input type="text" class="form-control-plaintext fw-bold"
                                                value="{{ $totalPoinPelanggaran }} poin" readonly>
                                            <small class="text-muted">Dihitung otomatis dari riwayat pelanggaran</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Nilai Konversi</label>
                                            <input type="text" class="form-control-plaintext fw-bold text-success"
                                                value="{{ $penilaian ? $penilaian->nilai_konversi : 'Akan dihitung' }}"
                                                readonly>
                                        </div>
                                    </div>
                                @else
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="form-label"
                                                for="nilai_asli_{{ strtolower($kriteria->kode_kriteria) }}">Nilai Asli
                                                (0-100) <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0" max="100"
                                                id="nilai_asli_{{ strtolower($kriteria->kode_kriteria) }}"
                                                name="nilai_asli_{{ strtolower($kriteria->kode_kriteria) }}"
                                                class="form-control nilai-input"
                                                data-kriteria-id="{{ $kriteria->id_kriteria }}"
                                                value="{{ old('nilai_asli_' . strtolower($kriteria->kode_kriteria), $penilaian ? $penilaian->nilai_asli : '') }}"
                                                required>
                                        </div>
                                    </div>
                                    @if ($penilaian)
                                        <div class="mt-2">
                                            <div class="alert alert-success mb-0 py-2">
                                                <small><strong>Nilai Konversi Saat Ini:</strong>
                                                    {{ $penilaian->nilai_konversi }}</small>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="mt-2" id="konversi_{{ strtolower($kriteria->kode_kriteria) }}"
                                        style="display: none;">
                                        <div class="alert alert-info mb-0 py-2">
                                            <small><strong>Nilai Konversi Baru:</strong> <span
                                                    class="nilai-konversi-text"></span></small>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('walikelas.penilaian.index') }}" class="btn btn-secondary"><i
                                class="bx bx-arrow-back me-1"></i> Kembali</a>
                        <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Update
                            Penilaian</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const subKriteriaData = @json(
                    $kriteriaList->mapWithKeys(function ($k) {
                        return [$k->id_kriteria => $k->subKriteria];
                    }));

                document.querySelectorAll('.nilai-input').forEach(input => {
                    input.addEventListener('input', function() {
                        const nilai = parseFloat(this.value);
                        const kriteriaId = this.dataset.kriteriaId;
                        const kodeKriteria = this.id.replace('nilai_asli_', '');
                        const konversiDiv = document.getElementById(`konversi_${kodeKriteria}`);
                        const konversiText = konversiDiv.querySelector('.nilai-konversi-text');

                        if (isNaN(nilai) || nilai < 0 || nilai > 100) {
                            konversiDiv.style.display = 'none';
                            return;
                        }

                        const subKriteria = subKriteriaData[kriteriaId];
                        if (!subKriteria) return;

                        const matched = subKriteria.find(sk => parseFloat(sk.nilai_awal) <= nilai &&
                            parseFloat(sk.nilai_akhir) >= nilai);
                        konversiText.textContent = matched ?
                            `${matched.bobot_subkriteria} (${matched.nama_subkriteria})` :
                            'Tidak ada sub kriteria yang sesuai';
                        konversiDiv.style.display = 'block';
                    });
                });
            });
        </script>
    @endpush
@endsection
