@extends('layouts.admin')
@section('title', 'Riwayat Pelanggaran Siswa')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Riwayat Pelanggaran</li>
                </ol>
            </nav>
            <a href="{{ route('admin.riwayatpelanggaran.create') }}" class="btn btn-sm btn-primary">
                <i class="bx bx-plus"></i>
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Filter Form -->
        <div class="card mb-4">
            <h5 class="card-header">Filter Pencarian</h5>
            <div class="card-body">
                <form action="{{ route('admin.riwayatpelanggaran.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="search">Cari Siswa / Pelanggaran</label>
                            <input type="text" class="form-control" id="search" name="search"
                                placeholder="NISN, nama siswa, atau pelanggaran..." value="{{ $search }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="tahun_ajaran">Tahun Ajaran</label>
                            <select class="form-select" id="tahun_ajaran" name="tahun_ajaran">
                                <option value="">Semua Tahun Ajaran</option>
                                @foreach ($tahunAjaranList as $ta)
                                    <option value="{{ $ta->id_ta }}" {{ $filterTA == $ta->id_ta ? 'selected' : '' }}>
                                        {{ $ta->tahun_ajaran }} - {{ $ta->semester }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label" for="kategori">Kategori Pelanggaran</label>
                            <select class="form-select" id="kategori" name="kategori">
                                <option value="">Semua Kategori</option>
                                @foreach ($kategoriList as $kat)
                                    <option value="{{ $kat }}" {{ $filterKategori == $kat ? 'selected' : '' }}>
                                        {{ str_replace('_', ' ', $kat) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label" for="kelas">Kelas</label>
                            <select class="form-select" id="kelas" name="kelas">
                                <option value="">Semua Kelas</option>
                                @foreach ($kelasList as $k)
                                    <option value="{{ $k->id_kelas }}"
                                        {{ $filterKelas == $k->id_kelas ? 'selected' : '' }}>
                                        {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="tanggal_mulai">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai"
                                value="{{ $filterTanggalMulai }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="tanggal_selesai">Tanggal Selesai</label>
                            <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai"
                                value="{{ $filterTanggalSelesai }}">
                        </div>
                    </div>
                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-search"></i> Cari
                        </button>
                        <a href="{{ route('admin.riwayatpelanggaran.index') }}" class="btn btn-label-secondary">
                            <i class="bx bx-x"></i> Reset Filter
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card">
            <h5 class="card-header">Data Riwayat Pelanggaran</h5>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>NISN / Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Kategori</th>
                            <th>Pelanggaran</th>
                            <th>Poin</th>
                            <th style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $item)
                            <tr>
                                <td>{{ ($riwayat->currentPage() - 1) * $riwayat->perPage() + $loop->iteration }}</td>
                                <td>
                                    <small class="text-muted">{{ $item->tanggal_kejadian->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    @if ($item->siswa)
                                        <strong>{{ $item->siswa->nisn }}</strong><br>
                                        <small>{{ $item->siswa->nama_siswa }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->siswa && $item->siswa->kelas)
                                        <span class="badge bg-label-info">{{ $item->siswa->kelas->nama_kelas }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->jenisPelanggaran)
                                        <span class="badge bg-label-primary">
                                            {{ str_replace('_', ' ', $item->jenisPelanggaran->kategori_pelanggaran) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->jenisPelanggaran)
                                        {{ Str::limit($item->jenisPelanggaran->nama_pelanggaran, 50) }}
                                    @endif
                                </td>
                                <td>
                                    @if ($item->jenisPelanggaran)
                                        @php
                                            $poin = $item->jenisPelanggaran->bobot_poin;
                                            $badgeClass = $poin >= 20 ? 'danger' : ($poin >= 10 ? 'warning' : 'info');
                                        @endphp
                                        <span class="badge bg-label-{{ $badgeClass }}">{{ $poin }} poin</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item"
                                                href="{{ route('admin.riwayatpelanggaran.show', $item->id_riwayat) }}">
                                                <i class="bx bx-show me-1"></i> Detail
                                            </a>
                                            <a class="dropdown-item"
                                                href="{{ route('admin.riwayatpelanggaran.edit', $item->id_riwayat) }}">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </a>
                                            <a class="dropdown-item btn-delete" href="javascript:void(0)"
                                                data-id="{{ $item->id_riwayat }}"
                                                data-name="{{ $item->siswa ? $item->siswa->nama_siswa : 'pelanggaran' }}">
                                                <i class="bx bx-trash me-1"></i> Hapus
                                            </a>
                                            <form id="delete-form-{{ $item->id_riwayat }}"
                                                action="{{ route('admin.riwayatpelanggaran.destroy', $item->id_riwayat) }}"
                                                method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data riwayat pelanggaran</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($riwayat->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Menampilkan {{ $riwayat->firstItem() }} - {{ $riwayat->lastItem() }} dari
                            {{ $riwayat->total() }} data
                        </div>
                        <nav aria-label="Page navigation">
                            {{ $riwayat->links('pagination::bootstrap-5') }}
                        </nav>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            // SweetAlert untuk Delete
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.dataset.id;
                    const itemName = this.dataset.name;

                    Swal.fire({
                        title: 'Hapus Riwayat Pelanggaran?',
                        html: `Yakin ingin menghapus riwayat pelanggaran siswa <strong>${itemName}</strong>?<br><small class="text-muted">Data yang dihapus tidak dapat dikembalikan!</small>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#8592a3',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById(`delete-form-${itemId}`).submit();
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
