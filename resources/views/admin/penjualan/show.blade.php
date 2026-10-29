@extends('layouts.adminlte')

@section('title', 'Nota Penjualan')
@section('page_title', 'Nota Penjualan: #' . $penjualan->no_nota)

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-receipt me-2"></i> Nota Penjualan #{{ $penjualan->no_nota }}
                    </h5>
                    <span class="badge bg-light text-dark fs-6">{{ ucfirst($penjualan->status_pembayaran) }}</span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-muted border-bottom pb-2">Informasi Transaksi</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="35%">Nomor Nota</th>
                                    <td>: <strong>{{ $penjualan->no_nota }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Tanggal</th>
                                    <td>: {{ $penjualan->tanggal ? $penjualan->tanggal->format('d F Y - H:i') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Kasir / Petugas</th>
                                    <td>: {{ $penjualan->user->name ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-muted border-bottom pb-2">Informasi Pelanggan</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="35%">Nama Customer</th>
                                    <td>: <strong>{{ $penjualan->customer->nama_customer ?? 'Umum' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>No Telepon</th>
                                    <td>: {{ $penjualan->customer->no_telepon ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td>: {{ $penjualan->customer->alamat ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <h6 class="fw-bold text-muted mb-2">Item Barang yang Dibeli</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th>Nama Produk</th>
                                    <th class="text-end">Harga Satuan</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($penjualan->detailPenjualan as $index => $item)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $item->produk->nama_produk ?? '-' }}</strong><br>
                                            <small class="text-muted">{{ $item->produk->kode_produk ?? '' }}</small>
                                        </td>
                                        <td class="text-end">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="text-center fw-bold">{{ (float) $item->qty }}
                                            {{ $item->produk->satuanDasar->nama_satuan ?? '' }}</td>
                                        <td class="text-end fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end fs-5">TOTAL PEMBAYARAN:</th>
                                    <th class="text-end fs-5 text-success fw-bold">Rp
                                        {{ number_format($penjualan->total, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('admin.penjualan.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali ke Riwayat
                    </a>
                    <div>
                        @if($penjualan->status_pengiriman !== 'terkirim')
                            <a href="{{ route('admin.penjualan.kirim.create', $penjualan->id) }}"
                                class="btn btn-primary me-2 fw-bold">
                                <i class="bi bi-truck"></i> Kirim Barang
                            </a>
                        @endif
                        <button type="button" class="btn btn-success" onclick="window.print()">
                            <i class="bi bi-printer"></i> Cetak Nota Struk
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection