@extends(request()->has('print') ? 'layouts.print' : 'layouts.adminlte')

@section('title', 'Laporan Penjualan')
@section('page_title', 'Laporan Transaksi Penjualan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-none d-print-block text-center border-bottom pb-2 mb-3">
            <h3 class="fw-bold mb-1">TOKO BAHAN BANGUNAN X</h3>
            <h5 class="fw-semibold mb-1">LAPORAN PENJUALAN (KASIR & PESANAN TOKO)</h5>
            <p class="small text-muted mb-0">
                Periode: <strong>{{ DateTime::createFromFormat('!m', (int) $bulan)->format('F') }} {{ $tahun }}</strong>
                @if($statusBayar)
                    | Status: <strong>{{ $statusBayar == 'lunas' ? 'Lunas' : 'Belum Lunas (Piutang)' }}</strong>
                @endif
            </p>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 d-print-none">
            <button type="button" onclick="cetakLaporan('{{ route('admin.laporan.penjualan', array_merge(request()->all(), ['print' => 1])) }}')"
                class="btn text-white"
                style="background-color: #2A9D8F; font-weight: 500; padding: 7px 16px;">
                <i class="bi bi-printer me-1"></i> Cetak Laporan
            </button>

            <form method="GET" action="{{ route('admin.laporan.penjualan') }}" class="d-flex align-items-center gap-2 flex-wrap">
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
                <select name="status_pembayaran" class="form-select form-select-sm" style="width: 160px;">
                    <option value="">Semua Status</option>
                    <option value="lunas" {{ $statusBayar == 'lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="belum_dibayar" {{ $statusBayar == 'belum_dibayar' ? 'selected' : '' }}>Belum Lunas (Piutang)</option>
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
                    <span class="small text-muted">Nota Penjualan</span>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm border-0 bg-white p-3">
                    <span class="text-muted small fw-bold">TOTAL PENJUALAN</span>
                    <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalNominal, 0, ',', '.') }}</h4>
                    <span class="small text-muted">Total Nilai Penjualan Barang</span>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm border-0 bg-white p-3">
                    <span class="text-muted small fw-bold">PENJUALAN LUNAS</span>
                    <h4 class="fw-bold mb-0 text-success">Rp {{ number_format($totalLunas, 0, ',', '.') }}</h4>
                    <span class="small text-muted">Total Piutang Terbayar</span>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm border-0 bg-white p-3">
                    <span class="text-muted small fw-bold">PIUTANG BERJALAN</span>
                    <h4 class="fw-bold mb-0 text-danger">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</h4>
                    <span class="small text-muted">Total Jatuh Tempo</span>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header py-2 text-white d-flex justify-content-between align-items-center d-print-none"
                style="background-color: #264653; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <h6 class="card-title fw-bold m-0 text-white">
                    <i class="bi bi-cart-check me-2"></i> Rincian Laporan Penjualan
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
                            <th>No Nota</th>
                            <th>Tanggal</th>
                            <th>Nama Customer</th>
                            <th class="text-center">Metode Bayar</th>
                            <th>Jatuh Tempo</th>
                            <th class="text-end">Total Penjualan</th>
                            <th class="text-center">Status Bayar</th>
                            <th class="text-center d-print-none">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penjualan as $index => $item)
                            <tr>
                                <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                                <td><span class="fw-bold text-dark">{{ $item->no_nota }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $item->customer->nama_customer ?? 'Pelanggan Umum' }}</span>
                                </td>
                                <td class="text-center">
                                    @if($item->customer_id && $item->status_pembayaran != 'lunas')
                                        <span class="badge bg-warning text-dark">Tempo (Piutang)</span>
                                    @else
                                        <span class="badge bg-success">Tunai / Cash</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->customer_id && $item->status_pembayaran != 'lunas')
                                        <span class="text-muted">Fleksibel</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @if($item->status_pembayaran == 'lunas')
                                        <span class="badge bg-success">Lunas</span>
                                    @else
                                        <span class="badge bg-danger">Belum Lunas</span>
                                    @endif
                                </td>
                                <td class="text-center d-print-none">
                                    <a href="{{ route('admin.penjualan.show', $item->id) }}" class="btn btn-sm btn-outline-info" title="Lihat Nota">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                    Tidak ada data penjualan pada periode filter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($penjualan->count() > 0)
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="6" class="text-end">TOTAL OMZET KESELURUHAN:</td>
                                <td class="text-end text-dark">Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
                                <td></td>
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
