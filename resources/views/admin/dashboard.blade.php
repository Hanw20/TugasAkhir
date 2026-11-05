@extends('layouts.adminlte')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard Utama')

@section('content')
    <!-- Banner Sambutan -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-success d-flex align-items-center mb-0" role="alert">
                <i class="fas fa-store fa-2x mr-3"></i>
                <div>
                    <h5 class="alert-heading mb-1 font-weight-bold">Selamat Datang di Sistem Informasi Toko Bahan Bangunan
                        "X"!</h5>
                    <p class="mb-0 text-muted">Ringkasan operasional transaksi, peringatan stok menipis, dan tagihan jatuh
                        tempo toko Anda.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 Kotak Kartu Ringkasan -->
    <div class="row">
        <!-- Penjualan Bulan Ini -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>Rp {{ number_format($totalPenjualanBulanIni, 0, ',', '.') }}</h3>
                    <p>Penjualan Bulan Ini ({{ $jumlahPenjualanBulanIni }} Transaksi)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <a href="{{ route('admin.penjualan.index') }}" class="small-box-footer">
                    Lihat Penjualan <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Pembelian Bulan Ini -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>Rp {{ number_format($totalPembelianBulanIni, 0, ',', '.') }}</h3>
                    <p>Pembelian Bulan Ini ({{ $jumlahPembelianBulanIni }} Faktur)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-truck-loading"></i>
                </div>
                <a href="{{ route('admin.pembelian.index') }}" class="small-box-footer">
                    Lihat Pembelian <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Peringatan Stok Menipis -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $produkStokMenipis->count() }} <sup style="font-size: 16px">Produk</sup></h3>
                    <p>Peringatan Stok Menipis</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="{{ route('admin.produk.index') }}" class="small-box-footer">
                    Periksa Stok <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Hutang Jatuh Tempo -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $hutangJatuhTempo->count() }} <sup style="font-size: 16px">Faktur</sup></h3>
                    <p>Hutang Belum Lunas / Tempo</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <a href="{{ route('admin.pembayaran-hutang.index') }}" class="small-box-footer">
                    Kelola Hutang <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- 2 Tabel Peringatan Penting -->
    <div class="row">
        <!-- Tabel Peringatan Stok Menipis -->
        <div class="col-md-6">
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-boxes mr-1 text-warning"></i> Daftar Produk Stok Menipis (Perlu Restock)
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Produk</th>
                                <th class="text-center">Sisa Stok</th>
                                <th class="text-center">Stok Min</th>
                                <th>Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($produkStokMenipis as $item)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $item->kode_produk }}</td>
                                    <td class="text-dark">{{ $item->nama_produk }}</td>
                                    <td class="text-center fw-bold text-danger">{{ number_format($item->stok, 0) }}</td>
                                    <td class="text-center text-secondary fw-semibold">
                                        {{ number_format($item->stok_minimum, 0) }}
                                    </td>
                                    <td><span
                                            class="badge bg-secondary text-white">{{ $item->satuanDasar->nama_satuan ?? '-' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">
                                        <i class="fas fa-check-circle text-success mr-1"></i> Semua stok produk dalam batas
                                        aman.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tabel Pengingat Hutang Pembelian Jatuh Tempo -->
        <div class="col-md-6">
            <div class="card card-danger card-outline">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-file-invoice-dollar mr-1 text-danger"></i> Tagihan Hutang Pembelian Supplier
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-sm mb-0">
                        <thead>
                            <tr>
                                <th>No Faktur</th>
                                <th>Supplier</th>
                                <th>Jatuh Tempo</th>
                                <th class="text-right">Total Tagihan</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hutangJatuhTempo as $beli)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $beli->no_faktur }}</td>
                                    <td class="text-dark">{{ $beli->supplier->nama_supplier ?? '-' }}</td>
                                    <td class="text-secondary">
                                        {{ $beli->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($beli->tanggal_jatuh_tempo)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="text-end fw-bold text-dark">Rp {{ number_format($beli->total, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        @if($beli->status_pembayaran == 'sebagian')
                                            <span class="badge bg-warning text-dark fw-bold">Sebagian</span>
                                        @else
                                            <span class="badge bg-danger text-white fw-bold">Belum Bayar</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">
                                        <i class="fas fa-check-circle text-success mr-1"></i> Tidak ada tagihan hutang yang
                                        belum lunas.
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