@extends('layouts.adminlte')

@section('title', 'Bukti Penerimaan Barang')
@section('page_title', 'Bukti Penerimaan Fisik Barang #' . ($penerimaan->pembelian->no_faktur ?? ''))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 10px; overflow: hidden;">
            <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #264653;">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-clipboard-check me-2"></i> Bukti Penerimaan Barang Fisik (Surat Jalan Gudang)
                </h5>
                <span class="badge" style="background-color: #D1FAE5; color: #065F46; font-weight: 600; padding: 6px 12px; border-radius: 6px;">Barang Masuk Gudang</span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-dark border-bottom pb-2">Informasi Kedatangan</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="40%">No Faktur Pembelian (PO)</th>
                                <td>: <a href="{{ route('admin.pembelian.show', $penerimaan->pembelian_id) }}" class="fw-bold" style="color: #2A9D8F;">{{ $penerimaan->pembelian->no_faktur }}</a></td>
                            </tr>
                            <tr>
                                <th>Tanggal & Waktu Masuk</th>
                                <td>: <strong>{{ $penerimaan->tanggal_terima ? $penerimaan->tanggal_terima->format('d F Y - H:i') : '-' }} WIB</strong></td>
                            </tr>
                            <tr>
                                <th>Petugas Penerima (Gudang)</th>
                                <td>: {{ $penerimaan->user->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Catatan Supir / Gudang</th>
                                <td>: <em>{{ $penerimaan->catatan ?? '-' }}</em></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-dark border-bottom pb-2">Informasi Supplier</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="35%">Nama Supplier</th>
                                <td>: <strong>{{ $penerimaan->pembelian->supplier->nama_supplier ?? '-' }}</strong></td>
                            </tr>
                            <tr>
                                <th>No Telepon</th>
                                <td>: {{ $penerimaan->pembelian->supplier->nomor_telepon ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Alamat</th>
                                <td>: {{ $penerimaan->pembelian->supplier->alamat ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <h6 class="fw-bold text-dark mb-2">Rincian Barang yang Diterima pada Sesi Ini</h6>
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="15%">Kode Produk</th>
                                <th>Nama Produk</th>
                                <th width="20%" class="text-center">Kuantitas Riil Diterima</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penerimaan->detailPenerimaan as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td><span class="badge" style="background-color: #F1F5F9; color: #475569; font-family: monospace;">{{ $item->detailPembelian->produk->kode_produk ?? '-' }}</span></td>
                                <td><strong>{{ $item->detailPembelian->produk->nama_produk ?? '-' }}</strong></td>
                                <td class="text-center">
                                    <span class="badge" style="background-color: #D1FAE5; color: #065F46; font-size: 0.9rem; padding: 6px 12px; border-radius: 6px;">
                                        + {{ (float)$item->qty_diterima }} {{ $item->detailPembelian->produk->satuanDasar->nama_satuan ?? 'Item' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between bg-white py-3 border-0">
                <a href="{{ route('admin.pembelian.penerimaan.index') }}" class="btn btn-sm" style="background-color: #E2E8F0; color: #334155; font-weight: 500; padding: 7px 16px; border-radius: 6px;">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat Penerimaan
                </a>
    
            </div>
        </div>
    </div>
</div>
@endsection