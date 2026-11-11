@extends('layouts.adminlte')

@section('page_title', 'Rincian Perhitungan Matematis')

@section('content')
<div class="container-fluid px-4">
    <!-- Header & Tombol Kembali -->
    <div class="d-flex justify-content-between align-items-center my-3">
        <div>
            <h4 class="mb-0 fw-bold" style="color: #264653;">
                <i class="fas fa-calculator me-2 text-primary"></i>Rincian & Transparansi Perhitungan Matematis
            </h4>
            <p class="text-muted small mb-0">Audit rincian matriks keputusan riil database, pembobotan AHP, dan rumus normalisasi SAW</p>
        </div>
        <a href="{{ route('admin.rekomendasi-supplier.index', ['produk_id' => $selectedProduk->id ?? '', 'sesi_id' => $sesiDipilih->id ?? '']) }}" 
           class="btn btn-sm btn-outline-secondary shadow-sm" style="border-radius: 6px;">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Hasil Rekomendasi
        </a>
    </div>

    @if ($selectedProduk && count($alternatif) > 0)
        <!-- Info Banner -->
        <div class="alert alert-light border shadow-sm mb-4" style="border-radius: 8px;">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <span class="text-muted small">Produk yang Dianalisis:</span>
                    <h6 class="mb-0 fw-bold text-dark">[{{ $selectedProduk->kode_produk }}] {{ $selectedProduk->nama_produk }}</h6>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="text-muted small">Sesi Bobot AHP Digunakan:</span>
                    <div class="fw-semibold text-dark">
                        Sesi {{ \Carbon\Carbon::parse($sesiDipilih->tanggal_hitung)->translatedFormat('d M Y H:i') }}
                        <span class="badge bg-success ms-1">CR: {{ number_format($sesiDipilih->cr_score, 4) }} &le; 0.1</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1. TABEL BOBOT PRIORITAS AHP (W_j) -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 8px;">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold" style="color: #264653;">
                    <i class="fas fa-balance-scale text-primary me-2"></i>1. Bobot Prioritas Kriteria dari Sesi AHP (W<sub>j</sub>)
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle mb-0 text-center">
                    <thead style="background-color: #F4F6F5;">
                        <tr>
                            @foreach ($kriteriaList as $k)
                                <th>
                                    <span class="badge bg-secondary mb-1">{{ $k->kode_kriteria }}</span><br>
                                    {{ $k->nama_kriteria }}<br>
                                    <small class="badge {{ $k->jenis_kriteria === 'cost' ? 'bg-danger' : 'bg-success' }} mt-1">
                                        {{ strtoupper($k->jenis_kriteria) }}
                                    </small>
                                </th>
                            @endforeach
                            <th style="background-color: #E8F5E9; width: 100px;">Total (&Sigma;)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @php $sumW = 0; @endphp
                            @foreach ($kriteriaList as $k)
                                @php 
                                    $w = $bobotAHP[$k->kode_kriteria] ?? 0;
                                    $sumW += $w;
                                @endphp
                                <td class="fw-bold fs-6">
                                    {{ number_format($w, 4) }}<br>
                                    <small class="text-muted fw-normal">({{ number_format($w * 100, 1) }}%)</small>
                                </td>
                            @endforeach
                            <td class="fw-bold fs-6" style="background-color: #E8F5E9; color: #2E7D32;">
                                {{ number_format($sumW, 4) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. TABEL MATRIKS KEPUTUSAN AWAL (X_ij) -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 8px;">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold" style="color: #264653;">
                    <i class="fas fa-table text-info me-2"></i>2. Matriks Keputusan Awal Nilai Riil Database (X<sub>ij</sub>)
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle mb-0 text-center">
                    <thead style="background-color: #F4F6F5;">
                        <tr>
                            <th class="text-start" style="min-width: 200px;">Nama Supplier</th>
                            @foreach ($kriteriaList as $k)
                                <th>
                                    <span class="badge bg-secondary">{{ $k->kode_kriteria }}</span> {{ $k->nama_kriteria }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($alternatif as $supId => $sup)
                            <tr>
                                <td class="text-start fw-semibold text-dark">{{ $sup->nama_supplier }}</td>
                                @foreach ($kriteriaList as $k)
                                    @php
                                        $val = $matriksKeputusan[$supId][$k->kode_kriteria] ?? 0;
                                    @endphp
                                    <td>
                                        @if ($k->kode_kriteria === 'K1')
                                            Rp {{ number_format($val, 0, ',', '.') }}
                                        @elseif(in_array($k->kode_kriteria, ['K2', 'K3', 'K4']))
                                            {{ number_format($val, 2) }} <small class="text-muted">/ 5</small>
                                        @elseif($k->kode_kriteria === 'K5')
                                            {{ $val }} <small class="text-muted">Hari</small>
                                        @elseif($k->kode_kriteria === 'K6')
                                            {{ $val }} <small class="text-muted">Hari</small>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. TABEL RINCIAN NORMALISASI SAW (R_ij) LENGKAP -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 8px;">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold" style="color: #264653;">
                    <i class="fas fa-divide text-danger me-2"></i>3. Rincian Perhitungan Normalisasi SAW Tiap Kriteria
                </h6>
                <small class="text-muted">Rumus Benefit: R = Nilai / Max | Rumus Cost: R = Min / Nilai</small>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead style="background-color: #F4F6F5;" class="text-center">
                        <tr>
                            <th style="width: 220px;" class="text-start">Kriteria</th>
                            <th style="width: 100px;">Sifat / Tipe</th>
                            <th style="width: 140px;">Acuan (Min / Max)</th>
                            <th style="width: 150px;">Rumus SAW</th>
                            <th class="text-start">Hasil Normalisasi per Supplier (R<sub>ij</sub>)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kriteriaList as $k)
                            @php
                                $kode = $k->kode_kriteria;
                                $isCost = ($k->jenis_kriteria === 'cost');
                                $acuan = $isCost ? ($minMax[$kode]['min'] ?? 1) : ($minMax[$kode]['max'] ?? 1);
                            @endphp
                            <tr>
                                <td class="fw-bold">
                                    <span class="badge bg-secondary me-1">{{ $kode }}</span>
                                    {{ $k->nama_kriteria }}
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $isCost ? 'bg-danger' : 'bg-success' }}">
                                        {{ strtoupper($k->jenis_kriteria) }}
                                    </span>
                                </td>
                                <td class="text-center fw-bold {{ $isCost ? 'text-danger' : 'text-success' }}">
                                    {{ $isCost ? 'Min: ' : 'Max: ' }}
                                    {{ is_numeric($acuan) && $acuan == intval($acuan) ? $acuan : number_format($acuan, 2) }}
                                </td>
                                <td class="text-center font-monospace text-muted small">
                                    {{ $isCost ? 'R = Min / Nilai' : 'R = Nilai / Max' }}
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($alternatif as $supId => $sup)
                                            @php
                                                $rawVal = $matriksKeputusan[$supId][$kode] ?? 0;
                                                $normVal = $matriksNormalisasi[$supId][$kode] ?? 0;
                                                $rawDisplay = is_numeric($rawVal) && $rawVal == intval($rawVal) ? $rawVal : number_format($rawVal, 2);
                                                $acuanDisplay = is_numeric($acuan) && $acuan == intval($acuan) ? $acuan : number_format($acuan, 2);
                                            @endphp
                                            <div class="border rounded px-2 py-1 bg-light small">
                                                <strong>{{ $sup->nama_supplier }}:</strong>
                                                <span class="text-primary fw-bold">{{ number_format($normVal, 4) }}</span>
                                                <span class="text-muted">
                                                    @if ($isCost)
                                                        ({{ $acuanDisplay }} / {{ $rawDisplay }})
                                                    @else
                                                        ({{ $rawDisplay }} / {{ $acuanDisplay }})
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="card shadow-sm border-0 p-5 text-center" style="border-radius: 8px;">
            <i class="fas fa-exclamation-circle text-warning fs-1 mb-3"></i>
            <h5 class="fw-bold text-muted">Data Produk Tidak Ditemukan</h5>
            <p class="text-muted small">Silakan pilih produk terlebih dahulu melalui halaman rekomendasi supplier.</p>
            <div class="mt-2">
                <a href="{{ route('admin.rekomendasi-supplier.index') }}" class="btn btn-sm text-white" style="background-color: #2A9D8F; border-radius: 6px; font-weight: 500;">
                    Kembali ke Halaman Rekomendasi
                </a>
            </div>
        </div>
    @endif
</div>
@endsection