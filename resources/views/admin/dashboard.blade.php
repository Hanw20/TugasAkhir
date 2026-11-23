@extends('layouts.adminlte')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard Utama')

@section('content')
    <!-- Banner Sambutan Sesuai Figma -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card" style="background-color: #EBF5F3;">
                <div class="card-body py-3 px-4 d-flex align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold" style="color: #264653;">
                            Selamat Datang di Sistem Informasi Toko Bahan Bangunan X
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kotak Ringkasan Statistik -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-5 g-3 mb-4">
        <!-- Penjualan Bulan Ini -->
        <div class="col">
            <div class="card h-100" style="background: #ffffff;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge p-2 me-2" style="background-color: #E0F2FE; color: #0284C7;">
                            <i class="bi bi-cash-stack fs-5"></i>
                        </span>
                        <span class="text-muted small fw-semibold">Penjualan Bulan Ini</span>
                    </div>
                    <h5 class="fw-bold mb-1 text-dark">Rp {{ number_format($totalPenjualanBulanIni, 0, ',', '.') }}</h5>
                    <small class="text-muted">{{ $jumlahPenjualanBulanIni }} Transaksi</small>
                </div>
            </div>
        </div>

        <!-- Pembelian Bulan Ini -->
        <div class="col">
            <div class="card h-100" style="background: #ffffff;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge p-2 me-2" style="background-color: #EDE9FE; color: #7C3AED;">
                            <i class="bi bi-cart3 fs-5"></i>
                        </span>
                        <span class="text-muted small fw-semibold">Pembelian Bulan Ini</span>
                    </div>
                    <h5 class="fw-bold mb-1 text-dark">Rp {{ number_format($totalPembelianBulanIni, 0, ',', '.') }}</h5>
                    <small class="text-muted">{{ $jumlahPembelianBulanIni }} Faktur</small>
                </div>
            </div>
        </div>

        <!-- Peringatan Stok Menipis -->
        <div class="col">
            <div class="card h-100" style="background: #ffffff;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge p-2 me-2" style="background-color: #FEF3C7; color: #D97706;">
                            <i class="bi bi-box-seam fs-5"></i>
                        </span>
                        <span class="text-muted small fw-semibold">Stok Menipis</span>
                    </div>
                    <h5 class="fw-bold mb-1 text-dark">{{ $produkStokMenipis->count() }} Produk</h5>
                    <span class="badge px-2 py-1" style="background-color: #FEF3C7; color: #92400E; font-size: 0.75rem;">Perlu Restock</span>
                </div>
            </div>
        </div>

        <!-- Tagihan Hutang Supplier -->
        <div class="col">
            <div class="card h-100" style="background: #ffffff;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge p-2 me-2" style="background-color: #FEE2E2; color: #DC2626;">
                            <i class="bi bi-file-earmark-arrow-down fs-5"></i>
                        </span>
                        <span class="text-muted small fw-semibold">Hutang Supplier</span>
                    </div>
                    <h5 class="fw-bold mb-1 text-dark">Rp {{ number_format($totalHutangBelumLunas, 0, ',', '.') }}</h5>
                    <span class="badge px-2 py-1" style="background-color: #FEE2E2; color: #991B1B; font-size: 0.75rem;">{{ $hutangJatuhTempo->count() }} Tagihan</span>
                </div>
            </div>
        </div>

        <!-- Tagihan Piutang Pelanggan -->
        <div class="col">
            <div class="card h-100" style="background: #ffffff;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge p-2 me-2" style="background-color: #DCFCE7; color: #16A34A;">
                            <i class="bi bi-wallet2 fs-5"></i>
                        </span>
                        <span class="text-muted small fw-semibold">Piutang Pelanggan</span>
                    </div>
                    <h5 class="fw-bold mb-1 text-dark">Rp {{ number_format($totalPiutangBelumLunas, 0, ',', '.') }}</h5>
                    <span class="badge px-2 py-1" style="background-color: #DCFCE7; color: #166534; font-size: 0.75rem;">{{ $piutangBelumLunas->count() }} Tagihan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Monitoring Hutang & Piutang Berdampingan -->
    <div class="row mb-4">
        <!-- Tagihan Hutang Pembelian Supplier -->
        <div class="col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                    <h5 class="card-title fw-bold text-dark mb-0 fs-6">
                        <i class="bi bi-file-invoice-dollar me-1" style="color: #E63946;"></i> Tagihan Hutang Pembelian Supplier
                    </h5>
                    <span class="badge" style="background-color: #FEE2E2; color: #B91C1C; font-size: 0.75rem;">Hutang Usaha</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-sm mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">No Faktur</th>
                                    <th>Supplier</th>
                                    <th>Jatuh Tempo</th>
                                    <th class="text-end">Sisa Tagihan</th>
                                    <th class="text-center pe-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hutangJatuhTempo as $beli)
                                    <tr>
                                        <td class="ps-3 fw-bold text-dark">{{ $beli->no_faktur }}</td>
                                        <td class="text-dark">{{ $beli->supplier->nama_supplier ?? '-' }}</td>
                                        <td class="text-secondary">
                                            {{ $beli->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($beli->tanggal_jatuh_tempo)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="text-end fw-bold text-danger">
                                            Rp {{ number_format($beli->sisa_hutang, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center pe-3">
                                            @if($beli->status_pembayaran == 'sebagian')
                                                <span class="badge" style="background-color: #FEF3C7; color: #B45309; font-weight: 600;">Sebagian</span>
                                            @else
                                                <span class="badge" style="background-color: #FEE2E2; color: #B91C1C; font-weight: 600;">Belum Bayar</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">
                                            <i class="bi bi-check-circle me-1" style="color: #2A9D8F;"></i> Tidak ada tagihan hutang yang belum lunas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monitoring Piutang Penjualan Pelanggan -->
        <div class="col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                    <h5 class="card-title fw-bold text-dark mb-0 fs-6">
                        <i class="bi bi-wallet2 me-1" style="color: #2A9D8F;"></i> Monitoring Piutang Penjualan Pelanggan
                    </h5>
                    <span class="badge" style="background-color: #DCFCE7; color: #166534; font-size: 0.75rem;">Piutang Usaha</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-sm mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">No Nota</th>
                                    <th>Pelanggan</th>
                                    <th>Tanggal</th>
                                    <th class="text-end">Sisa Piutang</th>
                                    <th class="text-center pe-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($piutangBelumLunas as $jual)
                                    <tr>
                                        <td class="ps-3 fw-bold text-dark">{{ $jual->no_nota }}</td>
                                        <td class="text-dark">{{ $jual->customer->nama_customer ?? 'Pelanggan Umum' }}</td>
                                        <td class="text-secondary">
                                            {{ $jual->tanggal ? \Carbon\Carbon::parse($jual->tanggal)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="text-end fw-bold" style="color: #2A9D8F;">
                                            Rp {{ number_format($jual->sisa_piutang, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center pe-3">
                                            @if($jual->status_pembayaran == 'sebagian')
                                                <span class="badge" style="background-color: #FEF3C7; color: #B45309; font-weight: 600;">Sebagian</span>
                                            @else
                                                <span class="badge" style="background-color: #FEE2E2; color: #B91C1C; font-weight: 600;">Belum Lunas</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">
                                            <i class="bi bi-check-circle me-1" style="color: #2A9D8F;"></i> Semua penjualan tempo pelanggan telah lunas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Peringatan Stok Menipis -->
    <div class="row">
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                    <h5 class="card-title fw-bold text-dark mb-0 fs-6">
                        <i class="bi bi-boxes me-1" style="color: #E76F51;"></i> Daftar Produk Stok Menipis
                    </h5>
                    <span class="badge" style="background-color: #FEF3C7; color: #B45309; font-size: 0.75rem;">Perlu Restock</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-sm mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Kode</th>
                                    <th>Nama Produk</th>
                                    <th class="text-center">Sisa Stok</th>
                                    <th class="text-center">Stok Minimum</th>
                                    <th class="pe-3">Satuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($produkStokMenipis as $item)
                                    <tr>
                                        <td class="ps-3 fw-bold text-dark">{{ $item->kode_produk }}</td>
                                        <td class="text-dark">{{ $item->nama_produk }}</td>
                                        <td class="text-center fw-bold" style="color: #E76F51;">{{ number_format($item->stok, 0) }}</td>
                                        <td class="text-center text-muted fw-semibold">{{ number_format($item->stok_minimum, 0) }}</td>
                                        <td class="pe-3"><span class="badge" style="background-color: #F1F5F9; color: #475569;">{{ $item->satuanDasar->nama_satuan ?? '-' }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">
                                            <i class="bi bi-check-circle me-1" style="color: #2A9D8F;"></i> Semua stok produk dalam batas aman.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection