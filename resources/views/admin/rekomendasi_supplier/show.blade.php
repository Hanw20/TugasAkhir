@extends('layouts.adminlte')

@section('title', 'Rincian Perhitungan SPK')
@section('page_title', 'Rincian Perhitungan Matematis (AHP & SAW)')

@section('content')
    <div class="container-fluid px-3">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <p class="text-muted small mb-0">Transparansi dan audit perhitungan matematis: integrasi pembobotan AHP dan perankingan SAW</p>
            </div>
            <a href="{{ route('admin.rekomendasi-supplier.index', ['produk_id' => $selectedProduk->id ?? '', 'sesi_id' => $sesiDipilih->id ?? '', 'analisis' => 1]) }}"
                class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Rekomendasi
            </a>
        </div>

        @if ($selectedProduk && count($alternatif) > 0)

            <div class="card mb-4">
                <div class="card-header py-2 text-white" style="background-color: #264653;">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-info-circle me-2"></i>Data  Analisis yang Digunakan</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <span class="text-muted small">Produk yang Dianalisis:</span>
                            <h5 class="fw-bold mb-0" style="color: #2A9D8F;">[{{ $selectedProduk->kode_produk }}] {{ $selectedProduk->nama_produk }}</h5>
                            <small class="text-muted">Kategori: {{ $selectedProduk->kategori->nama_kategori ?? '-' }} | Satuan: {{ $selectedProduk->satuanDasar->nama_satuan ?? 'Unit' }}</small>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted small">Sesi Pembobotan Kriteria AHP:</span>
                            <div class="fw-semibold text-dark">{{ $sesiDipilih->nama_sesi ?? 'Sesi AHP' }}</div>
                            <small class="text-muted">Tanggal Hitung: {{ \Carbon\Carbon::parse($sesiDipilih->tanggal_hitung)->format('d/m/Y H:i') }}</small>
                        </div>
                        <div class="col-md-3 text-md-end">
                            <span class="badge px-3 py-2 {{ ($ahpDetail['isConsistent'] ?? true) ? 'bg-success' : 'bg-danger' }}">
                                CR: {{ number_format($ahpDetail['cr'] ?? 0, 4) }} ({{ ($ahpDetail['isConsistent'] ?? true) ? 'KONSISTEN' : 'TIDAK KONSISTEN' }})
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            @if($ahpDetail)

                <div class="card mb-4">
                    <div class="card-header py-2 text-white d-flex justify-content-between align-items-center" style="background-color: #264653;">
                        <h6 class="mb-0 fw-semibold"><i class="bi bi-grid-3x3 me-2"></i>1. Matriks Perbandingan Berpasangan Skala Saaty (A)</h6>
                        <span class="badge" style="background-color: #2A9D8F;">Metode AHP</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle text-center m-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-start ps-3" style="width: 220px;">Kriteria</th>
                                        @foreach($kriteriaList as $k)
                                            <th>{{ $k->kode_kriteria }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kriteriaList as $i => $rowK)
                                        <tr>
                                            <td class="text-start ps-3 fw-semibold bg-light">
                                                {{ $rowK->kode_kriteria }} - {{ $rowK->nama_kriteria }}
                                            </td>
                                            @foreach($kriteriaList as $j => $colK)
                                                <td class="font-monospace {{ $i == $j ? 'bg-light text-muted fw-bold' : '' }}">
                                                    {{ number_format($ahpDetail['matrix'][$i][$j], 4) }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light fw-bold">
                                    <tr>
                                        <td class="text-start ps-3">Total Kolom (&Sigma;)</td>
                                        @foreach($kriteriaList as $j => $colK)
                                            <td class="font-monospace text-primary">
                                                {{ number_format($ahpDetail['colSums'][$j], 4) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header py-2 text-white d-flex justify-content-between align-items-center" style="background-color: #264653;">
                        <h6 class="mb-0 fw-semibold"><i class="bi bi-calculator me-2"></i>2. Normalisasi Matriks AHP & Bobot Prioritas Kriteria (W)</h6>
                        <span class="badge" style="background-color: #2A9D8F;">Metode AHP</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle text-center m-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-start ps-3" style="width: 220px;">Kriteria</th>
                                        @foreach($kriteriaList as $k)
                                            <th>{{ $k->kode_kriteria }}</th>
                                        @endforeach
                                        <th style="background-color: #F8FAFC;">Jumlah Baris</th>
                                        <th style="background-color: #E0F2FE; color: #0369A1; width: 140px;">Bobot Prioritas (W)</th>
                                        <th style="background-color: #E0F2FE; color: #0369A1; width: 100px;">Prioritas (%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalW = 0; @endphp
                                    @foreach($kriteriaList as $i => $rowK)
                                        @php
                                            $rowSum = array_sum($ahpDetail['normalized'][$i]);
                                            $w = $ahpDetail['eigenVector'][$i];
                                            $totalW += $w;
                                        @endphp
                                        <tr>
                                            <td class="text-start ps-3 fw-semibold bg-light">
                                                {{ $rowK->kode_kriteria }} - {{ $rowK->nama_kriteria }}
                                            </td>
                                            @foreach($kriteriaList as $j => $colK)
                                                <td class="font-monospace text-muted small">
                                                    {{ number_format($ahpDetail['normalized'][$i][$j], 4) }}
                                                </td>
                                            @endforeach
                                            <td class="font-monospace fw-semibold bg-light">
                                                {{ number_format($rowSum, 4) }}
                                            </td>
                                            <td class="font-monospace fw-bold" style="background-color: #F0F9FF; color: #0369A1;">
                                                {{ number_format($w, 4) }}
                                            </td>
                                            <td class="font-monospace fw-bold" style="background-color: #F0F9FF; color: #0369A1;">
                                                {{ number_format($w * 100, 2) }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light fw-bold">
                                    <tr>
                                        <td colspan="{{ count($kriteriaList) + 2 }}" class="text-end pe-3">Total Bobot:</td>
                                        <td class="font-monospace text-primary">{{ number_format($totalW, 4) }}</td>
                                        <td class="font-monospace text-primary">{{ number_format($totalW * 100, 2) }}%</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header py-2 text-white d-flex justify-content-between align-items-center" style="background-color: #264653;">
                        <h6 class="mb-0 fw-semibold"><i class="bi bi-check-circle me-2"></i>3. Uji Konsistensi AHP (Consistency Ratio)</h6>
                        <span class="badge" style="background-color: #2A9D8F;">Metode AHP</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="row align-items-center">
                            <div class="col-md-3 text-center border-end">
                                <div class="small text-muted text-uppercase fw-semibold">Consistency Ratio (CR)</div>
                                <h3 class="fw-bold my-1 {{ $ahpDetail['isConsistent'] ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($ahpDetail['cr'], 4) }}
                                </h3>
                                <span class="badge {{ $ahpDetail['isConsistent'] ? 'bg-success' : 'bg-danger' }}">
                                    {{ $ahpDetail['isConsistent'] ? 'KONSISTEN (CR <= 0.1)' : 'TIDAK KONSISTEN (CR > 0.1)' }}
                                </span>
                            </div>
                            <div class="col-md-9 ps-md-4">
                                <table class="table table-sm table-bordered m-0">
                                    <tbody>
                                        <tr>
                                            <th style="width: 250px;" class="bg-light ps-3">Lambda Max (&lambda;<sub>max</sub>)</th>
                                            <td class="font-monospace fw-bold ps-3">{{ number_format($ahpDetail['lambdaMax'], 4) }}</td>
                                            <td class="small text-muted ps-3">&lambda;<sub>max</sub> = &Sigma; (Total Kolom &times; Bobot W)</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light ps-3">Consistency Index (CI)</th>
                                            <td class="font-monospace fw-bold ps-3">{{ number_format($ahpDetail['ci'], 4) }}</td>
                                            <td class="small text-muted ps-3">CI = (&lambda;<sub>max</sub> - n) / (n - 1)</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light ps-3">Random Index (RI, n={{ count($kriteriaList) }})</th>
                                            <td class="font-monospace fw-bold ps-3">{{ number_format($ahpDetail['ri'], 2) }}</td>
                                            <td class="small text-muted ps-3">Standar acak Saaty untuk ordo n = {{ count($kriteriaList) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light ps-3">Consistency Ratio (CR)</th>
                                            <td class="font-monospace fw-bold ps-3 text-success">{{ number_format($ahpDetail['cr'], 4) }}</td>
                                            <td class="small text-muted ps-3">CR = CI / RI (Toleransi &le; 0.10)</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-header py-2 text-white d-flex justify-content-between align-items-center" style="background-color: #264653;">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-table me-2"></i>4. Matriks Keputusan Awal Nilai Riil Database (X)</h6>
                    <span class="badge" style="background-color: #2A9D8F;">Metode SAW</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle text-center m-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start ps-3" style="width: 240px;">Nama Supplier</th>
                                    @foreach ($kriteriaList as $k)
                                        <th>
                                            {{ $k->kode_kriteria }} - {{ $k->nama_kriteria }}<br>
                                            <small class="badge {{ $k->jenis_kriteria === 'cost' ? 'bg-danger' : 'bg-success' }}">
                                                {{ strtoupper($k->jenis_kriteria) }}
                                            </small>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($alternatif as $supId => $sup)
                                    <tr>
                                        <td class="text-start ps-3 fw-semibold">
                                            <span class="badge" style="background-color: #F1F5F9; color: #475569;">{{ $sup->kode_supplier }}</span>
                                            {{ $sup->nama_supplier }}
                                        </td>
                                        @foreach ($kriteriaList as $k)
                                            @php $val = $matriksKeputusan[$supId][$k->kode_kriteria] ?? 0; @endphp
                                            <td class="font-monospace">
                                                @if ($k->kode_kriteria === 'K1')
                                                    Rp {{ number_format($val, 0, ',', '.') }}
                                                @elseif(in_array($k->kode_kriteria, ['K2', 'K3', 'K4']))
                                                    {{ number_format($val, 2) }}
                                                @elseif(in_array($k->kode_kriteria, ['K5', 'K6']))
                                                    {{ is_numeric($val) && $val == intval($val) ? number_format($val, 1) : number_format($val, 2) }} hr
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light fw-bold font-monospace">
                                <tr>
                                    <td class="text-start ps-3 text-success">Nilai Maksimum (Max)</td>
                                    @foreach ($kriteriaList as $k)
                                        @php $maxVal = $minMax[$k->kode_kriteria]['max'] ?? 0; @endphp
                                        <td class="text-success">
                                            @if ($k->kode_kriteria === 'K1')
                                                Rp {{ number_format($maxVal, 0, ',', '.') }}
                                            @elseif(in_array($k->kode_kriteria, ['K2', 'K3', 'K4']))
                                                {{ number_format($maxVal, 2) }}
                                            @else
                                                {{ number_format($maxVal, 2) }}
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-start ps-3 text-danger">Nilai Minimum (Min)</td>
                                    @foreach ($kriteriaList as $k)
                                        @php $minVal = $minMax[$k->kode_kriteria]['min'] ?? 0; @endphp
                                        <td class="text-danger">
                                            @if ($k->kode_kriteria === 'K1')
                                                Rp {{ number_format($minVal, 0, ',', '.') }}
                                            @elseif(in_array($k->kode_kriteria, ['K2', 'K3', 'K4']))
                                                {{ number_format($minVal, 2) }}
                                            @else
                                                {{ number_format($minVal, 2) }}
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header py-2 text-white d-flex justify-content-between align-items-center" style="background-color: #264653;">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-percent me-2"></i>5. Matriks Normalisasi SAW (R)</h6>
                    <div class="small">
                        <span class="badge bg-success me-1">Benefit: R = X<sub>ij</sub> / Max</span>
                        <span class="badge bg-danger">Cost: R = Min / X<sub>ij</sub></span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle text-center m-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start ps-3" style="width: 240px;">Nama Supplier</th>
                                    @foreach ($kriteriaList as $k)
                                        <th>
                                            {{ $k->kode_kriteria }} ({{ strtoupper($k->jenis_kriteria) }})<br>
                                            <small class="text-muted">W: {{ number_format($bobotAHP[$k->kode_kriteria] ?? 0, 4) }}</small>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($alternatif as $supId => $sup)
                                    <tr>
                                        <td class="text-start ps-3 fw-semibold bg-light">
                                            <span class="badge" style="background-color: #E2E8F0; color: #475569;">{{ $sup->kode_supplier }}</span>
                                            {{ $sup->nama_supplier }}
                                        </td>
                                        @foreach ($kriteriaList as $k)
                                            @php
                                                $kode = $k->kode_kriteria;
                                                $rVal = $matriksNormalisasi[$supId][$kode] ?? 0;
                                            @endphp
                                            <td class="font-monospace fw-semibold {{ $rVal == 1.0 ? 'text-success' : 'text-primary' }}">
                                                {{ number_format($rVal, 4) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header py-2 text-white d-flex justify-content-between align-items-center" style="background-color: #264653;">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-award me-2"></i>6. Nilai Preferensi Akhir (V) & Peringkat Rekomendasi</h6>
                    <small class="text-white-50">Rumus: V<sub>i</sub> = &Sigma; (R<sub>ij</sub> &times; W<sub>j</sub>)</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle text-center m-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;">Peringkat</th>
                                    <th class="text-start ps-3" style="width: 240px;">Nama Supplier</th>
                                    <th class="text-start ps-3">Rincian Perhitungan (&Sigma; R<sub>ij</sub> &times; W<sub>j</sub>)</th>
                                    <th style="width: 140px; background-color: #E0F2FE; color: #0369A1;">Skor Preferensi (V)</th>
                                    <th style="width: 180px;">Status Rekomendasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ranking as $idx => $item)
                                    @php
                                        $rank = $idx + 1;
                                        $sup = $item['supplier'];
                                        $v = $item['skor_preferensi'];
                                    @endphp
                                    <tr class="{{ $rank == 1 ? 'table-light fw-bold' : '' }}">
                                        <td>
                                            @if($rank == 1)
                                                <span class="badge bg-success px-2 py-1">#1</span>
                                            @elseif($rank == 2)
                                                <span class="badge bg-primary px-2 py-1">#2</span>
                                            @elseif($rank == 3)
                                                <span class="badge bg-warning text-dark px-2 py-1">#3</span>
                                            @else
                                                <span class="badge bg-secondary px-2 py-1">#{{ $rank }}</span>
                                            @endif
                                        </td>
                                        <td class="text-start ps-3">
                                            <span class="badge" style="background-color: #E2E8F0; color: #475569;">{{ $sup->kode_supplier }}</span>
                                            {{ $sup->nama_supplier }}
                                        </td>
                                        <td class="text-start ps-3 font-monospace small text-muted">
                                            @php $terms = []; @endphp
                                            @foreach($kriteriaList as $k)
                                                @php
                                                    $det = $item['perkalian_detail'][$k->kode_kriteria] ?? null;
                                                    if ($det) {
                                                        $terms[] = "(" . number_format($det['r'], 4) . " &times; " . number_format($det['w'], 4) . ")";
                                                    }
                                                @endphp
                                            @endforeach
                                            {!! implode(' + ', $terms) !!}
                                        </td>
                                        <td class="font-monospace fs-6 fw-bold" style="background-color: #F0F9FF; color: #0369A1;">
                                            {{ number_format($v, 4) }}
                                        </td>
                                        <td>
                                            @if($rank == 1)
                                                <span class="badge bg-success px-2 py-1">Rekomendasi Utama</span>
                                            @elseif($rank == 2)
                                                <span class="badge bg-primary px-2 py-1">Alternatif Ke-2</span>
                                            @else
                                                <span class="badge bg-secondary px-2 py-1">Alternatif Lainnya</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header py-2 text-white d-flex justify-content-between align-items-center" style="background-color: #264653;">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-bar-chart me-2"></i>7. Komparasi Hasil Rekomendasi Antar Metode SPK</h6>
                    <small class="text-white-50">Perbandingan ranking metode hybrid vs tunggal</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle text-center m-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;">Peringkat</th>
                                    <th style="background-color: #E0F2FE; color: #0369A1; width: 30%;">AHP-SAW (Metode Utama)</th>
                                    <th style="width: 30%;">SAW (Tunggal / Equal Weight)</th>
                                    <th style="width: 30%;">AHP (Tunggal)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for($i = 0; $i < count($ranking); $i++)
                                    <tr>
                                        <td class="fw-bold">#{{ $i + 1 }}</td>
                                        <td class="text-start ps-3" style="background-color: #F0F9FF;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>{{ $ranking[$i]['supplier']->nama_supplier }}</span>
                                                <span class="badge bg-success font-monospace">{{ number_format($ranking[$i]['skor_preferensi'], 4) }}</span>
                                            </div>
                                        </td>
                                        <td class="text-start ps-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>{{ $rankingSawMurni[$i]['supplier']->nama_supplier }}</span>
                                                <span class="badge bg-primary font-monospace">{{ number_format($rankingSawMurni[$i]['skor_preferensi'], 4) }}</span>
                                            </div>
                                        </td>
                                        <td class="text-start ps-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>{{ $rankingAhpMurni[$i]['supplier']->nama_supplier }}</span>
                                                <span class="badge bg-warning text-dark font-monospace">{{ number_format($rankingAhpMurni[$i]['skor_preferensi'], 4) }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        @else
            <div class="card p-5 text-center">
                <i class="bi bi-exclamation-circle text-warning fs-1 mb-3"></i>
                <h5 class="fw-bold text-muted">Data Produk Tidak Ditemukan</h5>
                <p class="text-muted small">Silakan pilih produk terlebih dahulu melalui halaman rekomendasi supplier.</p>
                <div class="mt-2">
                    <a href="{{ route('admin.rekomendasi-supplier.index') }}" class="btn btn-sm text-white"
                        style="background-color: #2A9D8F; font-weight: 500;">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Halaman Rekomendasi
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection
