@extends('layouts.adminlte')

@section('title', 'Rekomendasi Supplier (SAW)')
@section('page_title', 'Rekomendasi Supplier Terbaik (Metode SAW)')

@section('content')
    <div class="container-fluid">

        <!-- CARD 1: PILIH PRODUK & PILIH SESI BOBOT AHP -->
        <div class="card card-outline card-primary shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title fw-bold m-0"><i class="bi bi-search me-2"></i>Parameter Rekomendasi Pengadaan Supplier
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.rekomendasi-supplier.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label fw-bold">Pilih Produk yang Ingin Direstock</label>
                        <select name="produk_id" class="form-select" required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach($daftarProduk as $p)
                                <option value="{{ $p->id }}" {{ (isset($selectedProduk) && $selectedProduk->id == $p->id) ? 'selected' : '' }}>
                                    [{{ $p->kode_produk }}] {{ $p->nama_produk }} (Stok: {{ $p->stok }}
                                    {{ $p->satuanDasar->nama_satuan ?? '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold">Pilih Sesi Pembobotan Kriteria AHP</label>
                        <select name="sesi_id" class="form-select">
                            @foreach($daftarSesiAhp as $s)
                                <option value="{{ $s->id }}" {{ (isset($sesiDipilih) && $sesiDipilih->id == $s->id) ? 'selected' : '' }}>
                                    {{ $s->nama_sesi }} ({{ \Carbon\Carbon::parse($s->tanggal_hitung)->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 fw-bold">
                            <i class="bi bi-funnel-fill me-1"></i> Analisis
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if($selectedProduk)
            <!-- INFORMASI PRODUK TERPILIH & BOBOT AHP -->
            <div class="row mb-4">
                <div class="col-md-5">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-info text-white">
                            <h6 class="card-title fw-bold m-0"><i class="bi bi-box-seam me-2"></i>Produk yang Dianalisis</h6>
                        </div>
                        <div class="card-body">
                            <h5 class="fw-bold text-primary">{{ $selectedProduk->nama_produk }}</h5>
                            <p class="text-muted mb-2">Kode: <code>{{ $selectedProduk->kode_produk }}</code> | Kategori:
                                {{ $selectedProduk->kategori->nama_kategori ?? '-' }}</p>
                            <div class="d-flex justify-content-between border-top pt-2">
                                <span>Stok Fisik Gudang:</span>
                                <span class="badge bg-secondary fs-6">{{ $selectedProduk->stok }}
                                    {{ $selectedProduk->satuan->nama_satuan ?? '' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                            <h6 class="card-title fw-bold m-0"><i class="bi bi-sliders me-2"></i>Bobot Kriteria (Sumber: AHP)
                            </h6>
                            <a href="{{ route('admin.sesi-perhitungan-ahp.index') }}" class="btn btn-xs btn-outline-light">
                                <i class="bi bi-pencil-square"></i> Ubah Bobot AHP
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm table-bordered m-0 text-center align-middle">
                                <thead class="table-light">
                                    <tr>
                                        @foreach($kriteriaList as $k)
                                            <th>
                                                <div>{{ $k->nama_kriteria }}</div>
                                                <small
                                                    class="badge {{ $k->jenis_kriteria == 'benefit' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ strtoupper($k->jenis_kriteria) }}
                                                </small>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="fw-bold font-monospace text-primary">
                                        @foreach($kriteriaList as $k)
                                            <td>{{ number_format($bobotAHP[$k->kode_kriteria] ?? 0.2, 4) }}</td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            @if(count($ranking) > 0)
                <!-- TABEL HASIL PERANGKINGAN (HASIL UTAMA SPK) -->
                <div class="card card-outline card-success shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="card-title fw-bold text-success m-0">
                            <i class="bi bi-trophy-fill me-2 text-warning"></i> Hasil Rekomendasi Supplier Terbaik (Metode SAW)
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle m-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 80px;" class="text-center">Ranking</th>
                                        <th>Kode & Nama Supplier</th>
                                        <th>Kontak / Telp</th>
                                        <th class="text-end" style="width: 180px;">Skor Preferensi (V<sub>i</sub>)</th>
                                        <th class="text-center" style="width: 150px;">Keputusan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ranking as $index => $r)
                                        <tr class="{{ $index == 0 ? 'table-success fw-bold' : '' }}">
                                            <td class="text-center">
                                                @if($index == 0)
                                                    <span class="badge bg-warning text-dark fs-6"><i class="bi bi-trophy-fill me-1"></i>
                                                        1</span>
                                                @elseif($index == 1)
                                                    <span class="badge bg-secondary fs-6">2</span>
                                                @elseif($index == 2)
                                                    <span class="badge bg-brown fs-6 text-dark" style="background:#cd7f32;">3</span>
                                                @else
                                                    <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-secondary font-monospace">{{ $r['supplier']->kode_supplier }}</span>
                                                <span class="ms-1">{{ $r['supplier']->nama_supplier }}</span>
                                            </td>
                                            <td>{{ $r['supplier']->nomor_telepon ?? '-' }}</td>
                                            <td class="text-end font-monospace fs-5 text-primary">
                                                {{ number_format($r['skor_preferensi'], 4) }}
                                            </td>
                                            <td class="text-center">
                                                @if($index == 0)
                                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Paling
                                                        Direkomendasikan</span>
                                                @else
                                                    <span class="badge bg-light text-muted">Alternatif</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- DETAIL PERHITUNGAN TRANSPARAN (MATRIKS KEPUTUSAN & NORMALISASI) -->
                <div class="card card-outline card-secondary shadow-sm mb-4">
                    <div class="card-header">
                        <h6 class="card-title fw-bold m-0"><i class="bi bi-table me-2"></i>Detail Transparansi Perhitungan SAW</h6>
                    </div>
                    <div class="card-body">
                        <!-- 1. Matriks Keputusan -->
                        <h6 class="fw-bold text-muted mb-2">1. Matriks Keputusan Awal (X)</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-sm table-bordered align-middle text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>Supplier</th>
                                        <th>K1 - Harga (Rp)</th>
                                        <th>K2 - Kualitas (1-5)</th>
                                        <th>K3 - Pelayanan (1-5)</th>
                                        <th>K4 - Pengiriman (Hari)</th>
                                        <th>K5 - Tempo (Hari)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ranking as $r)
                                        <tr>
                                            <td class="text-start fw-bold">{{ $r['supplier']->nama_supplier }}</td>
                                            <td class="text-end font-monospace">Rp
                                                {{ number_format($r['nilai_keputusan']['K1'], 0, ',', '.') }}</td>
                                            <td>{{ $r['nilai_keputusan']['K2'] }}</td>
                                            <td>{{ $r['nilai_keputusan']['K3'] }}</td>
                                            <td>{{ $r['nilai_keputusan']['K4'] }} Hari</td>
                                            <td>{{ $r['nilai_keputusan']['K5'] }} Hari</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- 2. Matriks Normalisasi -->
                        <h6 class="fw-bold text-muted mb-2">2. Matriks Normalisasi (R)</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>Supplier</th>
                                        <th>R1 (Harga)</th>
                                        <th>R2 (Kualitas)</th>
                                        <th>R3 (Pelayanan)</th>
                                        <th>R4 (Pengiriman)</th>
                                        <th>R5 (Tempo)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ranking as $r)
                                        <tr>
                                            <td class="text-start fw-bold">{{ $r['supplier']->nama_supplier }}</td>
                                            <td class="font-monospace">{{ number_format($r['nilai_normalisasi']['K1'], 4) }}</td>
                                            <td class="font-monospace">{{ number_format($r['nilai_normalisasi']['K2'], 4) }}</td>
                                            <td class="font-monospace">{{ number_format($r['nilai_normalisasi']['K3'], 4) }}</td>
                                            <td class="font-monospace">{{ number_format($r['nilai_normalisasi']['K4'], 4) }}</td>
                                            <td class="font-monospace">{{ number_format($r['nilai_normalisasi']['K5'], 4) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            @else
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Belum ada supplier yang dihubungkan ke produk <b>{{ $selectedProduk->nama_produk }}</b>. Silakan buka menu
                    <b>Supplier &rarr; Detail Supplier</b> untuk menghubungkan produk ini ke supplier yang memasoknya.
                </div>
            @endif

        @endif

    </div>
@endsection