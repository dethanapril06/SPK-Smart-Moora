@extends('layouts.kepalasekolah')
@section('title', 'Data Kriteria')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('kepalasekolah.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Data Kriteria</li>
            </ol>
        </nav>

        {{-- Kriteria --}}
        <div class="card mb-4">
            <h5 class="card-header">Kriteria Penilaian</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Kode</th>
                            <th>Nama Kriteria</th>
                            <th class="text-center">Jenis</th>
                            <th class="text-center">Bobot</th>
                            <th class="text-center">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kriteriaList as $kriteria)
                            @php
                                $pct = $totalBobot > 0 ? round(($kriteria->bobot / $totalBobot) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td><strong>{{ $kriteria->kode_kriteria }}</strong></td>
                                <td>{{ $kriteria->nama_kriteria }}</td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-label-{{ $kriteria->jenis_kriteria == 'Benefit' ? 'success' : 'danger' }}">
                                        {{ $kriteria->jenis_kriteria }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $kriteria->bobot }}</td>
                                <td class="text-center">{{ $pct }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <th colspan="4" class="text-end">Total Bobot:</th>
                            <th class="text-center">{{ $totalBobot }}</th>
                            <th class="text-center">100%</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Sub Kriteria --}}
        <div class="card">
            <h5 class="card-header">Sub Kriteria</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Kriteria</th>
                            <th>Nama Sub Kriteria</th>
                            <th class="text-center">Rentang Nilai</th>
                            <th class="text-center">Bobot Sub Kriteria</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subKriteriaList as $sub)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="badge bg-label-primary">{{ $sub->kriteria->kode_kriteria ?? '-' }}</span>
                                    {{ $sub->kriteria->nama_kriteria ?? '' }}
                                </td>
                                <td>{{ $sub->nama_subkriteria }}</td>
                                <td class="text-center">
                                    <span class="badge bg-label-info">{{ $sub->nilai_awal }} -
                                        {{ $sub->nilai_akhir }}</span>
                                </td>
                                <td class="text-center"><strong>{{ $sub->bobot_subkriteria }}</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data sub kriteria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
