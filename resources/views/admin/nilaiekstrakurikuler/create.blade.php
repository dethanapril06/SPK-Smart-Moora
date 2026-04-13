@extends('layouts.admin')
@section('title', 'Tambah Nilai Ekstrakurikuler')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.nilaiekstrakurikuler.index') }}">Nilai Ekstrakurikuler</a>
                </li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <h5 class="card-header">Form Tambah Nilai Ekstrakurikuler</h5>
            <div class="card-body">
                <form action="{{ route('admin.nilaiekstrakurikuler.store') }}" method="POST" id="formEkskul">
                    @csrf

                    {{-- Filter Tahun Ajaran & Kelas --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="id_ta">Tahun Ajaran <span
                                    class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                                <select id="id_ta" name="id_ta"
                                    class="form-select @error('id_ta') is-invalid @enderror">
                                    <option value="">-- Pilih Tahun Ajaran --</option>
                                    @foreach ($tahunAjaranList as $ta)
                                        <option value="{{ $ta->id_ta }}"
                                            {{ (old('id_ta', $selectedTA) == $ta->id_ta) ? 'selected' : '' }}>
                                            {{ $ta->tahun_ajaran }} - Semester {{ $ta->semester }}
                                            {{ $ta->is_active ? '(Aktif)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('id_ta')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="id_kelas">Kelas <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-book"></i></span>
                                <select id="id_kelas" name="id_kelas" class="form-select">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach ($kelasList as $kelas)
                                        <option value="{{ $kelas->id_kelas }}"
                                            {{ (old('id_kelas', $selectedKelas) == $kelas->id_kelas) ? 'selected' : '' }}>
                                            {{ $kelas->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Pilih Siswa --}}
                    <div class="mb-3">
                        <label class="form-label" for="id_siswa">Siswa <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-user"></i></span>
                            <select id="id_siswa" name="id_siswa"
                                class="form-select @error('id_siswa') is-invalid @enderror">
                                <option value="">-- Pilih Siswa --</option>
                                @foreach ($siswaList as $siswa)
                                    <option value="{{ $siswa->id_siswa }}"
                                        {{ old('id_siswa') == $siswa->id_siswa ? 'selected' : '' }}>
                                        {{ $siswa->nama_siswa }} ({{ $siswa->nisn }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('id_siswa')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @if ($selectedTA && $selectedKelas && $siswaList->count() == 0)
                            <div class="form-text text-warning">Tidak ada siswa ditemukan untuk filter ini.</div>
                        @endif
                    </div>

                    {{-- Ekskul yang sudah ada (loaded via AJAX) --}}
                    <div id="existingEkskulSection" class="mb-4" style="display: none;">
                        <div class="alert alert-light border mb-0">
                            <h6 class="alert-heading mb-2">
                                <i class="bx bx-list-check me-1"></i> Ekstrakurikuler yang sudah terdaftar
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 40px;">No</th>
                                            <th>Nama Ekskul</th>
                                            <th style="width: 130px;">Predikat</th>
                                        </tr>
                                    </thead>
                                    <tbody id="existingEkskulBody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- Daftar Ekstrakurikuler Baru --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0"><i class="bx bx-list-plus me-1"></i> Tambah Ekstrakurikuler Baru</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnTambahRow">
                            <i class="bx bx-plus me-1"></i> Tambah Ekskul
                        </button>
                    </div>

                    @error('ekskul')
                        <div class="alert alert-danger py-2">{{ $message }}</div>
                    @enderror

                    <div id="ekskulContainer">
                        {{-- Row template akan di-clone via JS --}}
                        <div class="ekskul-row row mb-3 align-items-end">
                            <div class="col-md-1 text-center">
                                <label class="form-label d-block">#</label>
                                <span class="ekskul-number badge bg-label-primary rounded-pill">1</span>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Nama Ekstrakurikuler <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="ekskul[0][nama_ekskul]"
                                    placeholder="Contoh: Pramuka" value="{{ old('ekskul.0.nama_ekskul') }}" required>
                                @error('ekskul.0.nama_ekskul')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Predikat <span class="text-danger">*</span></label>
                                <select class="form-select" name="ekskul[0][predikat]" required>
                                    <option value="Sangat Baik"
                                        {{ old('ekskul.0.predikat') == 'Sangat Baik' ? 'selected' : '' }}>Sangat Baik
                                    </option>
                                    <option value="Baik"
                                        {{ old('ekskul.0.predikat', 'Baik') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="Cukup"
                                        {{ old('ekskul.0.predikat') == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                    <option value="Kurang"
                                        {{ old('ekskul.0.predikat') == 'Kurang' ? 'selected' : '' }}>Kurang</option>
                                </select>
                                @error('ekskul.0.predikat')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 text-center">
                                <label class="form-label d-block">&nbsp;</label>
                                <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-remove-row"
                                    title="Hapus baris" disabled>
                                    <i class="bx bx-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.nilaiekstrakurikuler.index', ['tahun_ajaran' => $selectedTA, 'kelas' => $selectedKelas]) }}"
                            class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Simpan Semua
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Dynamic filter: reload page when TA or Kelas changes to load siswa list
                $('#id_ta, #id_kelas').on('change', function() {
                    var ta = $('#id_ta').val();
                    var kelas = $('#id_kelas').val();
                    if (ta && kelas) {
                        window.location.href = "{{ route('admin.nilaiekstrakurikuler.create') }}" +
                            "?tahun_ajaran=" + ta + "&kelas=" + kelas;
                    }
                });

                // Load existing ekskul when siswa is selected
                $('#id_siswa').on('change', function() {
                    var idSiswa = $(this).val();
                    var idTa = $('#id_ta').val();
                    var $section = $('#existingEkskulSection');
                    var $tbody = $('#existingEkskulBody');

                    if (!idSiswa || !idTa) {
                        $section.slideUp();
                        $tbody.empty();
                        return;
                    }

                    $.ajax({
                        url: "{{ route('admin.nilaiekstrakurikuler.getEkskul') }}",
                        type: 'GET',
                        data: {
                            id_siswa: idSiswa,
                            id_ta: idTa
                        },
                        beforeSend: function() {
                            $tbody.html(
                                '<tr><td colspan="3" class="text-center"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Memuat...</td></tr>'
                            );
                            $section.slideDown();
                        },
                        success: function(data) {
                            $tbody.empty();
                            if (data.length === 0) {
                                $section.slideUp();
                                return;
                            }

                            var predikatBadge = function(predikat) {
                                var color = 'danger';
                                if (predikat === 'Sangat Baik') color = 'success';
                                else if (predikat === 'Baik') color = 'primary';
                                else if (predikat === 'Cukup') color = 'warning';
                                return '<span class="badge bg-label-' + color + '">' +
                                    predikat + '</span>';
                            };

                            $.each(data, function(index, item) {
                                $tbody.append(
                                    '<tr>' +
                                    '<td>' + (index + 1) + '</td>' +
                                    '<td>' + item.nama_ekskul + '</td>' +
                                    '<td>' + predikatBadge(item.predikat) +
                                    '</td>' +
                                    '</tr>'
                                );
                            });

                            $section.slideDown();
                        },
                        error: function() {
                            $tbody.html(
                                '<tr><td colspan="3" class="text-center text-danger">Gagal memuat data</td></tr>'
                            );
                        }
                    });
                });

                // Trigger load jika sudah ada siswa yang dipilih (misal dari old input)
                if ($('#id_siswa').val()) {
                    $('#id_siswa').trigger('change');
                }

                // Dynamic ekskul rows
                var rowIndex = 1;
                var $container = $('#ekskulContainer');

                $('#btnTambahRow').on('click', function() {
                    var newRow = $('<div class="ekskul-row row mb-3 align-items-end">' +
                        '<div class="col-md-1 text-center">' +
                        '<span class="ekskul-number badge bg-label-primary rounded-pill">' + (rowIndex +
                            1) + '</span>' +
                        '</div>' +
                        '<div class="col-md-5">' +
                        '<input type="text" class="form-control" name="ekskul[' + rowIndex +
                        '][nama_ekskul]" placeholder="Contoh: Pramuka" required>' +
                        '</div>' +
                        '<div class="col-md-4">' +
                        '<select class="form-select" name="ekskul[' + rowIndex + '][predikat]" required>' +
                        '<option value="Sangat Baik">Sangat Baik</option>' +
                        '<option value="Baik" selected>Baik</option>' +
                        '<option value="Cukup">Cukup</option>' +
                        '<option value="Kurang">Kurang</option>' +
                        '</select>' +
                        '</div>' +
                        '<div class="col-md-2 text-center">' +
                        '<button type="button" class="btn btn-sm btn-icon btn-label-danger btn-remove-row" title="Hapus baris">' +
                        '<i class="bx bx-trash"></i>' +
                        '</button>' +
                        '</div>' +
                        '</div>');

                    $container.append(newRow);
                    rowIndex++;
                    updateRowNumbers();
                    updateRemoveButtons();
                });

                $container.on('click', '.btn-remove-row', function() {
                    $(this).closest('.ekskul-row').remove();
                    updateRowNumbers();
                    updateRemoveButtons();
                });

                function updateRowNumbers() {
                    $container.find('.ekskul-row').each(function(index) {
                        $(this).find('.ekskul-number').text(index + 1);
                    });
                }

                function updateRemoveButtons() {
                    var rows = $container.find('.ekskul-row');
                    rows.each(function() {
                        $(this).find('.btn-remove-row').prop('disabled', rows.length <= 1);
                    });
                }
            });
        </script>
    @endpush
@endsection
