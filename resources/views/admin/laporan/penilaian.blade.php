@extends(request()->has('print') ? 'layouts.print' : 'layouts.adminlte')

@section('title', 'Laporan Penilaian Supplier')
@section('page_title', 'Laporan Penilaian Kriteria Kinerja Supplier')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-none d-print-block text-center border-bottom pb-2 mb-3">
            <h3 class="fw-bold mb-1">TOKO BAHAN BANGUNAN X</h3>
            <h5 class="fw-semibold mb-1">LAPORAN PENILAIAN KRITERIA SUPPLIER</h5>
            <p class="small text-muted mb-0">
                Periode: <strong>{{ DateTime::createFromFormat('!m', (int) $bulan)->format('F') }} {{ $tahun }}</strong>
                @if($kriteriaId)
                    | Kriteria: <strong>{{ $kriteriaList->firstWhere('id', $kriteriaId)->nama_kriteria ?? '' }}</strong>
                @endif
                @if($supplierId)
                    | Supplier: <strong>{{ $suppliers->firstWhere('id', $supplierId)->nama_supplier ?? '' }}</strong>
                @endif
                @if($produkId)
                    | Produk: <strong>{{ $produks->firstWhere('id', $produkId)->nama_produk ?? '' }}</strong>
                @endif
            </p>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 d-print-none">
            <button type="button" onclick="cetakLaporan('{{ route('admin.laporan.penilaian', array_merge(request()->all(), ['print' => 1])) }}')"
                class="btn text-white"
                style="background-color: #2A9D8F; font-weight: 500; padding: 7px 16px;">
                <i class="bi bi-printer me-1"></i> Cetak Laporan
            </button>

            <form method="GET" action="{{ route('admin.laporan.penilaian') }}" class="d-flex align-items-center gap-2 flex-wrap">
                <label class="mb-0 fw-semibold text-muted me-1">Filter:</label>
                <select name="bulan" class="form-select form-select-sm" style="width: 130px;">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ sprintf('%02d', $m) }}" {{ $bulan == sprintf('%02d', $m) ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </select>
                <select name="tahun" class="form-select form-select-sm" style="width: 95px;">
                    @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <select name="kriteria_supplier_id" class="form-select form-select-sm" style="width: 170px;">
                    <option value="">Semua Kriteria</option>
                    @foreach($kriteriaList as $k)
                        <option value="{{ $k->id }}" {{ $kriteriaId == $k->id ? 'selected' : '' }}>
                            {{ $k->kode_kriteria }} - {{ $k->nama_kriteria }}
                        </option>
                    @endforeach
                </select>
                <select name="supplier_id" class="form-select form-select-sm" style="width: 160px;">
                    <option value="">Semua Supplier</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ $supplierId == $sup->id ? 'selected' : '' }}>
                            {{ $sup->nama_supplier }}
                        </option>
                    @endforeach
                </select>
                <select name="produk_id" class="form-select form-select-sm" style="width: 160px;">
                    <option value="">Semua Produk</option>
                    @foreach($produks as $p)
                        <option value="{{ $p->id }}" {{ $produkId == $p->id ? 'selected' : '' }}>
                            {{ $p->nama_produk }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-secondary">
                    <i class="bi bi-filter"></i> Terapkan
                </button>
            </form>
        </div>

        <div class="card shadow-sm">
            <div class="card-header py-2 text-white d-flex justify-content-between align-items-center d-print-none"
                style="background-color: #264653; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <h6 class="card-title fw-bold m-0 text-white">
                    <i class="bi bi-clipboard-data me-2"></i> Daftar Riwayat Penilaian Kriteria Supplier
                </h6>
                <span class="badge bg-light text-dark">
                    Periode: {{ DateTime::createFromFormat('!m', (int) $bulan)->format('F') }} {{ $tahun }}
                </span>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-bordered table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Tanggal</th>
                            <th>Supplier</th>
                            <th class="text-center">Kriteria</th>
                            <th>Produk / Transaksi</th>
                            <th class="text-end">Nilai Asli</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $index => $item)
                            <tr>
                                <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_penilaian)->format('d/m/Y') }}</td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $item->supplier->nama_supplier ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $kode = $item->kriteria->kode_kriteria ?? '';
                                    @endphp
                                    @if($kode == 'K1')
                                        <span class="badge bg-primary px-2 py-1">K1 - Harga Beli</span>
                                    @elseif($kode == 'K2')
                                        <span class="badge bg-success px-2 py-1">K2 - Kualitas Produk</span>
                                    @elseif($kode == 'K3')
                                        <span class="badge bg-info text-dark px-2 py-1">K3 - Kecepatan Respon</span>
                                    @elseif($kode == 'K4')
                                        <span class="badge px-2 py-1" style="background-color: #6f42c1; color: white;">K4 - Garansi</span>
                                    @elseif($kode == 'K5')
                                        <span class="badge bg-warning text-dark px-2 py-1">K5 - Waktu Pengiriman</span>
                                    @elseif($kode == 'K6')
                                        <span class="badge bg-secondary px-2 py-1">K6 - Tempo Pembayaran</span>
                                    @else
                                        <span class="badge bg-dark px-2 py-1">{{ $kode }}</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $namaProduk = $item->detailPenerimaanPembelian->detailPembelian->produk->nama_produk ?? null;
                                        if (!$namaProduk && !empty($item->keterangan)) {
                                            if (preg_match('/Harga beli (.+?) pada faktur/i', $item->keterangan, $m)) {
                                                $namaProduk = trim($m[1]);
                                            }
                                        }
                                    @endphp
                                    @if($namaProduk)
                                        <span>{{ $namaProduk }}</span>
                                    @elseif($kode == 'K5')
                                        <span class="text-muted">Pengiriman Pesanan</span>
                                    @elseif($kode == 'K6')
                                        <span class="text-muted">Pembayaran Hutang</span>
                                    @elseif($kode == 'K3' || $kode == 'K4')
                                        <span class="text-muted">Penilaian Supplier</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold">
                                    @if($kode == 'K1')
                                        <span class="text-dark">Rp {{ number_format($item->nilai, 0, ',', '.') }}</span>
                                    @elseif($kode == 'K2' || $kode == 'K3' || $kode == 'K4')
                                        <span class="text-warning">★</span> <span class="text-dark">{{ number_format($item->nilai, 1) }}</span> <small class="text-muted">/ 5</small>
                                    @elseif($kode == 'K5' || $kode == 'K6')
                                        <span class="text-dark">{{ number_format($item->nilai, 0) }} Hari</span>
                                    @else
                                        <span>{{ $item->nilai }}</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ str_replace(['Kebijakan Garansi', ': -', ':-'], ['Garansi', ' berkala', ' berkala'], $item->keterangan ?? '-') }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                    Tidak ada data penilaian kriteria pada periode filter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-none d-print-block mt-4">
            <div class="row justify-content-end text-center">
                <div class="col-4">
                    <p class="mb-5">Surabaya, {{ date('d F Y') }}<br>Mengetahui, Pemilik Toko</p>
                    <p class="fw-bold text-decoration-underline mb-0">( ........................................ )</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
