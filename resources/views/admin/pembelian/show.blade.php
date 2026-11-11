@extends('layouts.adminlte')

@section('title', 'Detail Faktur Pembelian')
@section('page_title', 'Faktur Pembelian: ' . $pembelian->no_faktur)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 10px; overflow: hidden;">
            <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #264653;">
                <h5 class="card-title mb-0 fw-bold"><i class="bi bi-receipt me-2"></i> Faktur Pembelian #{{ $pembelian->no_faktur }}</h5>
                <span class="badge" style="background-color: #FEF3C7; color: #B45309; font-weight: 600; padding: 6px 12px; border-radius: 6px;">{{ ucfirst($pembelian->status) }}</span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-dark">Informasi Faktur</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="35%">No Faktur</th>
                                <td>: <strong>{{ $pembelian->no_faktur }}</strong></td>
                            </tr>
                            <tr>
                                <th>Tanggal</th>
                                <td>: {{ $pembelian->tanggal->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th>Petugas Input</th>
                                <td>: {{ $pembelian->user->name ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-dark">Informasi Supplier</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="35%">Supplier</th>
                                <td>: <strong>{{ $pembelian->supplier->nama_supplier ?? '-' }}</strong></td>
                            </tr>
                            <tr>
                                <th>No Telepon</th>
                                <td>: {{ $pembelian->supplier->nomor_telepon ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Alamat</th>
                                <td>: {{ $pembelian->supplier->alamat ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Kode Produk</th>
                                <th>Nama Produk</th>
                                <th>Harga Beli Satuan</th>
                                <th class="text-center">Qty Masuk</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pembelian->detailPembelian as $index => $detail)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><span class="badge" style="background-color: #F1F5F9; color: #475569; font-family: monospace;">{{ $detail->produk->kode_produk ?? '-' }}</span></td>
                                <td><strong>{{ $detail->produk->nama_produk ?? '-' }}</strong></td>
                                <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                <td class="text-center fw-bold">
                                    {{ (int)$detail->qty }} {{ $detail->produk->satuanDasar->nama_satuan ?? '' }}
                                </td>
                                <td class="text-end fw-bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="5" class="text-end fs-6 fw-bold">TOTAL AKHIR:</th>
                                <th class="text-end fs-6 fw-bold" style="color: #264653;">
                                    Rp {{ number_format($pembelian->total, 0, ',', '.') }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between bg-white py-3 border-0">
                <a href="{{ route('admin.pembelian.index') }}" class="btn btn-sm" style="background-color: #E2E8F0; color: #334155; font-weight: 500; padding: 7px 16px; border-radius: 6px;">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat
                </a>
                <div>
                    <!-- Tombol Masuk ke Form Penerimaan Barang -->
                    @if($pembelian->status_penerimaan !== 'terima_penuh')
                        <a href="{{ route('admin.pembelian.terima.create', $pembelian->id) }}" class="btn btn-sm text-white fw-bold shadow-sm" style="background-color: #2A9D8F; padding: 7px 18px; border-radius: 6px;">
                            <i class="bi bi-box-arrow-in-down me-1"></i> Terima Barang Datang
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection