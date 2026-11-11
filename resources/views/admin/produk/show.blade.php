@extends('layouts.adminlte')

@section('title', 'Detail Produk')
@section('page_title', 'Detail Produk: ' . $produk->nama_produk)

@section('content')
    <div class="row">
        <!-- 1. CARD INFORMASI LENGKAP PRODUK (FULL WIDTH DI ATAS) -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0" style="border-radius: 10px; overflow: hidden;">
                <div class="card-header text-white" style="background-color: #264653;">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-box-seam me-2"></i> Informasi Lengkap Produk</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0 align-middle">
                            <tbody>
                                <tr>
                                    <th width="20%" class="ps-3">Kode Produk</th>
                                    <td><span class="badge" style="background-color: #E0F2FE; color: #0369A1; font-family: monospace; font-size: 0.9rem;">{{ $produk->kode_produk }}</span></td>
                                </tr>
                                <tr>
                                    <th class="ps-3">Nama Produk</th>
                                    <td class="fw-bold fs-6">{{ $produk->nama_produk }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-3">Kategori</th>
                                    <td>{{ $produk->kategori->nama_kategori ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-3">Merek</th>
                                    <td>{{ $produk->merek->nama_merek ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-3">Satuan Dasar</th>
                                    <td>{{ $produk->satuanDasar->nama_satuan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-3">Harga Beli Rata-Rata</th>
                                    <td class="fw-bold">Rp {{ number_format($produk->harga_beli_rata_rata ?? $produk->harga_beli, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-3">Harga Beli Terakhir</th>
                                    <td>
                                        @if($produk->harga_beli_terakhir)
                                            <strong class="fs-6" style="color: #2A9D8F;">Rp {{ number_format($produk->harga_beli_terakhir, 0, ',', '.') }}</strong>
                                        @else
                                            <span class="text-muted">Rp {{ number_format($produk->harga_beli, 0, ',', '.') }} <em>(Belum ada update pembelian)</em></span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="ps-3">Harga Jual (Konsumen)</th>
                                    <td><strong class="fs-6" style="color: #264653;">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</strong></td>
                                </tr>
                                <tr>
                                    <th class="ps-3">Stok Saat Ini</th>
                                    <td>
                                        <strong class="fs-6">{{ (float) $produk->stok }} {{ $produk->satuanDasar->nama_satuan ?? '' }}</strong>
                                        @if($produk->stok <= $produk->stok_minimum)
                                            <span class="badge ms-2" style="background-color: #FEE2E2; color: #B91C1C;">Stok Menipis!</span>
                                        @else
                                            <span class="badge ms-2" style="background-color: #D1FAE5; color: #065F46;">Stok Aman</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="ps-3">Batas Stok Minimum</th>
                                    <td>{{ (float) $produk->stok_minimum }} {{ $produk->satuanDasar->nama_satuan ?? '' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light py-2">
                    <a href="{{ route('admin.produk.index') }}" class="btn btn-sm" style="background-color: #E2E8F0; color: #334155; font-weight: 500;">
                        <i class="bi bi-arrow-left me-1"></i> Kembali Ke Daftar Produk
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. CARD TRACKING RIWAYAT PEMBELIAN (FULL WIDTH DI BAWAH) -->
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 10px; overflow: hidden;">
                <div class="card-header text-white" style="background-color: #264653;">
                    <h5 class="card-title fw-bold m-0"><i class="bi bi-clock-history me-2"></i> Tracking Riwayat Pembelian Produk</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle m-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th>No Faktur</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Supplier</th>
                                    <th class="text-center">Qty Dipesan</th>
                                    <th class="text-end">Harga Beli Satuan</th>
                                    <th class="text-end">Subtotal</th>
                                    <th width="10%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($produk->detailPembelian()->with('pembelian.supplier')->latest()->take(10)->get() as $idx => $dp)
                                    <tr>
                                        <td class="text-center">{{ $idx + 1 }}</td>
                                        <td>
                                            <span class="badge bg-secondary font-monospace fs-6">
                                                {{ $dp->pembelian->no_faktur ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $dp->pembelian && $dp->pembelian->tanggal ? $dp->pembelian->tanggal->format('d/m/Y') : '-' }}
                                        </td>
                                        <td>
                                            <strong>{{ $dp->pembelian->supplier->nama_supplier ?? '-' }}</strong>
                                        </td>
                                        <td class="text-center fw-bold">
                                            {{ (float) $dp->qty }} {{ $produk->satuanDasar->nama_satuan ?? '' }}
                                        </td>
                                        <td class="text-end fw-bold">
                                            Rp {{ number_format($dp->harga_satuan, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end fw-bold text-primary">
                                            Rp {{ number_format($dp->subtotal, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            @if($dp->pembelian)
                                                <a href="{{ route('admin.pembelian.show', $dp->pembelian->id) }}" class="btn btn-sm text-decoration-none" style="background-color: #E0F2FE; color: #0369A1; font-weight: 500;">
                                                    Faktur
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                            <em>Belum ada riwayat transaksi pembelian untuk produk ini.</em>
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