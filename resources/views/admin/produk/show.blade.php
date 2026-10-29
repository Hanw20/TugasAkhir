@extends('layouts.adminlte')

@section('title', 'Detail Produk')
@section('page_title', 'Detail Produk: ' . $produk->nama_produk)

@section('content')
    <div class="row">
        <!-- Kolom Kiri: Informasi Utama Produk -->
        <div class="col-md-7">
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0"><i class="bi bi-box-seam me-2"></i> Informasi Lengkap Produk</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <tr>
                            <th width="35%">Kode Produk</th>
                            <td><span class="badge bg-secondary fs-6">{{ $produk->kode_produk }}</span></td>
                        </tr>
                        <tr>
                            <th>Nama Produk</th>
                            <td><strong>{{ $produk->nama_produk }}</strong></td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td>{{ $produk->kategori->nama_kategori ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Merek</th>
                            <td>{{ $produk->merek->nama_merek ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Satuan Dasar</th>
                            <td>{{ $produk->satuanDasar->nama_satuan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Harga Beli Rata-Rata</th>
                            <td><strong>Rp {{ number_format($produk->harga_beli_rata_rata, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <th>Harga Beli Terakhir</th>
                            <td>
                                @if($produk->harga_beli_terakhir)
                                    <strong class="text-primary fs-6">Rp
                                        {{ number_format($produk->harga_beli_terakhir, 0, ',', '.') }}</strong>
                                @else
                                    <span class="text-muted">Rp {{ number_format($produk->harga_beli, 0, ',', '.') }} <em>(Belum
                                            ada update pembelian)</em></span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Harga Jual (Konsumen)</th>
                            <td><strong class="text-success fs-6">Rp
                                    {{ number_format($produk->harga_jual, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <th>Stok Saat Ini</th>
                            <td>
                                <strong>{{ (float) $produk->stok }} {{ $produk->satuanDasar->nama_satuan ?? '' }}</strong>

                                @if($produk->stok <= $produk->stok_minimum)
                                    <span class="badge bg-danger ms-2">Stok Menipis!</span>
                                @else
                                    <span class="badge bg-success ms-2">Stok Aman</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Batas Stok Minimum</th>
                            <td>{{ (float) $produk->stok_minimum }} {{ $produk->satuanDasar->nama_satuan ?? '' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.produk.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Produk
                    </a>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Tracking Histori Pembelian Terakhir -->
        <div class="col-md-5">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="bi bi-clock-history me-2"></i> Tracking Riwayat Pembelian</h5>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-bordered table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tgl & Faktur</th>
                                <th>Supplier</th>
                                <th>Qty</th>
                                <th>Harga Beli</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($produk->detailPembelian()->with('pembelian.supplier')->latest()->take(5)->get() as $dp)
                                <tr>
                                    <td>
                                        <strong>{{ $dp->pembelian->tanggal ? $dp->pembelian->tanggal->format('d/m/y') : '-' }}</strong><br>
                                        <small class="text-muted">{{ $dp->pembelian->no_faktur ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <small><strong>{{ $dp->pembelian->supplier->nama_supplier ?? '-' }}</strong></small>
                                    </td>
                                    <td>{{ (float) $dp->qty }}</td>
                                    <td class="text-primary fw-bold">
                                        Rp {{ number_format($dp->harga_satuan, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">
                                        <em>Belum ada transaksi pembelian untuk produk ini.</em>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection