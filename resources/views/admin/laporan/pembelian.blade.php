@extends(request()->has('print') ? 'layouts.print' : 'layouts.adminlte')

@section('title', 'Laporan Pembelian')
@section('page_title', 'Laporan Transaksi Pembelian Barang')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-none d-print-block text-center border-bottom pb-2 mb-3">
            <h3 class="fw-bold mb-1">TOKO BAHAN BANGUNAN X</h3>
            <h5 class="fw-semibold mb-1">LAPORAN PEMBELIAN (PENGADAAN BARANG)</h5>
            <p class="small text-muted mb-0">
                Periode: <strong>{{ DateTime::createFromFormat('!m', (int) $bulan)->format('F') }} {{ $tahun }}</strong>
                @if($supplierId)
                    | Supplier: <strong>{{ $suppliers->firstWhere('id', $supplierId)->nama_supplier ?? '' }}</strong>
                @endif
                @if($statusBayar)
                    | Status: <strong>{{ $statusBayar == 'lunas' ? 'Lunas' : 'Belum Lunas (Hutang)' }}</strong>
                @endif
            </p>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 d-print-none">
            <button type="button" onclick="cetakLaporan('{{ route('admin.laporan.pembelian', array_merge(request()->all(), ['print' => 1])) }}')"
                class="btn text-white"
                style="background-color: #2A9D8F; font-weight: 500; padding: 7px 16px;">
                <i class="bi bi-printer me-1"></i> Cetak Laporan
            </button>

            <form method="GET" action="{{ route('admin.laporan.pembelian') }}" class="d-flex align-items-center gap-2 flex-wrap">
                <label class="mb-0 fw-semibold text-muted me-1">Filter:</label>
                <select name="bulan" class="form-select form-select-sm" style="width: 140px;">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ sprintf('%02d', $m) }}" {{ $bulan == sprintf('%02d', $m) ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </select>
                <select name="tahun" class="form-select form-select-sm" style="width: 100px;">
                    @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <select name="supplier_id" class="form-select form-select-sm" style="width: 170px;">
                    <option value="">Semua Supplier</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ $supplierId == $sup->id ? 'selected' : '' }}>
                            {{ $sup->nama_supplier }}
                        </option>
                    @endforeach
                </select>
                <select name="status_pembayaran" class="form-select form-select-sm" style="width: 160px;">
                    <option value="">Semua Status</option>
                    <option value="lunas" {{ $statusBayar == 'lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="belum_dibayar" {{ $statusBayar == 'belum_dibayar' ? 'selected' : '' }}>Belum Lunas (Hutang)</option>
                </select>
                <button type="submit" class="btn btn-sm btn-secondary">
                    <i class="bi bi-filter"></i> Terapkan
                </button>
            </form>
        </div>

        <div class="row g-3 mb-3 d-print-none">
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm border-0 bg-white p-3">
                    <span class="text-muted small fw-bold">TOTAL TRANSAKSI</span>
                    <h4 class="fw-bold mb-0 text-dark">{{ number_format($totalTransaksi, 0, ',', '.') }}</h4>
                    <span class="small text-muted">Nota Faktur PO Pembelian</span>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm border-0 bg-white p-3">
                    <span class="text-muted small fw-bold">TOTAL PEMBELIAN</span>
                    <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalNominal, 0, ',', '.') }}</h4>
                    <span class="small text-muted">Total Nilai Pembelian Barang</span>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm border-0 bg-white p-3">
                    <span class="text-muted small fw-bold">TOTAL LUNAS</span>
                    <h4 class="fw-bold mb-0 text-success">Rp {{ number_format($totalLunas, 0, ',', '.') }}</h4>
                    <span class="small text-muted">Pembayaran Selesai</span>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm border-0 bg-white p-3">
                    <span class="text-muted small fw-bold">HUTANG BERJALAN</span>
                    <h4 class="fw-bold mb-0 text-danger">Rp {{ number_format($totalHutang, 0, ',', '.') }}</h4>
                    <span class="small text-muted">Total Jatuh Tempo</span>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header py-2 text-white d-flex justify-content-between align-items-center d-print-none"
                style="background-color: #264653; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <h6 class="card-title fw-bold m-0 text-white">
                    <i class="bi bi-receipt me-2"></i> Rincian Laporan Pembelian
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
                            <th>No Faktur</th>
                            <th>Tanggal</th>
                            <th>Supplier</th>
                            <th>Jatuh Tempo</th>
                            <th class="text-end">Total Pembelian</th>
                            <th class="text-center">Status Barang</th>
                            <th class="text-center">Status Bayar</th>
                            <th class="text-center d-print-none">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pembelian as $index => $item)
                            <tr>
                                <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                                <td><span class="fw-bold text-dark">{{ $item->no_faktur }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $item->supplier->nama_supplier ?? '-' }}</span>
                                </td>
                                <td>
                                    @if($item->tanggal_jatuh_tempo)
                                        {{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @if($item->status_penerimaan == 'terima_penuh')
                                        <span class="badge bg-success">Diterima Penuh</span>
                                    @elseif($item->status_penerimaan == 'sebagian')
                                        <span class="badge bg-warning text-dark">Sebagian</span>
                                    @else
                                        <span class="badge bg-secondary">Belum Diterima</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->status_pembayaran == 'lunas')
                                        <span class="badge bg-success">Lunas</span>
                                    @else
                                        <span class="badge bg-danger">Hutang (Tempo)</span>
                                    @endif
                                </td>
                                <td class="text-center d-print-none">
                                    <a href="{{ route('admin.pembelian.show', $item->id) }}" class="btn btn-sm btn-outline-info" title="Lihat Faktur">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                    Tidak ada data pembelian pada periode filter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($pembelian->count() > 0)
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="5" class="text-end">TOTAL KESELURUHAN:</td>
                                <td class="text-end text-dark">Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
                                <td colspan="2"></td>
                                <td class="d-print-none"></td>
                            </tr>
                        </tfoot>
                    @endif
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
