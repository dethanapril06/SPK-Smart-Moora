@extends('layouts.walikelas')
@section('title', 'Tambah Penilaian Siswa')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('walikelas.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('walikelas.penilaian.index') }}">Penilaian Siswa</a></li>
                <li class="breadcrumb-item active">Tambah Penilaian</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">Form Penilaian Siswa</h5>
            <div class="card-body">
                <form action="{{ route('walikelas.penilaian.store') }}" method="POST" id="formPenilaian">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label" for="id_siswa">Siswa <span class="text-danger">*</span></label>
                            <select id="id_siswa" name="id_siswa"
                                class="form-select @error('id_siswa') is-invalid @enderror" required>
                                <option value="">Pilih Siswa</option>
                                @foreach ($siswaList as $siswa)
                                    <option value="{{ $siswa->id_siswa }}"
                                        {{ old('id_siswa', $selectedSiswa) == $siswa->id_siswa ? 'selected' : '' }}>
                                        {{ $siswa->nisn }} - {{ $siswa->nama_siswa }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_siswa')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="id_ta">Tahun Ajaran <span class="text-danger">*</span></label>
                            <select id="id_ta" name="id_ta" class="form-select @error('id_ta') is-invalid @enderror"
                                required>
                                @foreach ($tahunAjaranList as $ta)
                                    <option value="{{ $ta->id_ta }}"
                                        {{ old('id_ta', $selectedTA ?? ($tahunAjaranAktif ? $tahunAjaranAktif->id_ta : '')) == $ta->id_ta ? 'selected' : '' }}>
                                        {{ $ta->tahun_ajaran }} - {{ $ta->semester }}
                                        {{ $ta->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_ta')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3 text-primary"><i class="bx bx-star me-2"></i>Input Nilai Kriteria</h6>

                    @foreach ($kriteriaList as $kriteria)
                        <div class="card mb-3 kriteria-section">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-0">{{ $kriteria->kode_kriteria }} - {{ $kriteria->nama_kriteria }}
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
                                            <input type="text" id="c5_display" class="form-control-plaintext fw-bold"
                                                value="{{ $totalPoinPelanggaran }} poin" readonly>
                                            <small class="text-muted">Dihitung otomatis dari riwayat pelanggaran</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Nilai Konversi</label>
                                            <input type="text" id="c5_konversi"
                                                class="form-control-plaintext fw-bold text-success" value="-" readonly>
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
                                                placeholder="Masukkan nilai 0-100"
                                                value="{{ old('nilai_asli_' . strtolower($kriteria->kode_kriteria)) }}"
                                                required>
                                            <small class="text-muted">Akan dikonversi ke sub kriteria berdasarkan rentang
                                                nilai</small>
                                        </div>
                                    </div>
                                    <div class="mt-2" id="konversi_{{ strtolower($kriteria->kode_kriteria) }}"
                                        style="display: none;">
                                        <div class="alert alert-info mb-0 py-2">
                                            <small><strong>Nilai Konversi:</strong> <span
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
                        <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Simpan
                            Penilaian</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const siswaSelect = document.getElementById('id_siswa');
                const taSelect = document.getElementById('id_ta');
                const subKriteriaData = @json(
                    $kriteriaList->mapWithKeys(function ($k) {
                        return [$k->id_kriteria => $k->subKriteria];
                    }));

                function updateC5() {
                    const siswaId = siswaSelect.value;
                    const taId = taSelect.value;
                    if (!siswaId || !taId) return;

                    fetch(`{{ route('walikelas.penilaian.getC5') }}?id_siswa=${siswaId}&id_ta=${taId}`)
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('c5_display').value = `${data.total_poin} poin`;
                            document.getElementById('c5_konversi').value = data.nilai_konversi || 'N/A';
                        });
                }

                siswaSelect.addEventListener('change', updateC5);
                taSelect.addEventListener('change', updateC5);

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

                if (siswaSelect.value && taSelect.value) updateC5();
            });
        </script>
    @endpush
@endsection
