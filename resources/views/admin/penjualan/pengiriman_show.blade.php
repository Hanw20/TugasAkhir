@extends('layouts.adminlte')

@section('title', 'Surat Jalan Pengiriman')
@section('page_title', 'Surat Jalan Pengiriman #' . ($pengiriman->penjualan->no_nota ?? ''))

@section('content')
<div class="row">
 <div class="col-12">
 <div class="card" >
 <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #264653;">
 <h5 class="card-title mb-0 fw-bold">
 <i class="bi bi-file-earmark-text me-2"></i> Surat Jalan Pengiriman Barang (Toko ke Customer)
 </h5>
 <span class="badge" style="background-color: #E0F2FE; color: #0369A1; font-weight: 600; padding: 6px 12px;">Barang Keluar Gudang</span>
 </div>
 <div class="card-body">
 <div class="row mb-4">
 <div class="col-md-6">
 <h6 class="fw-bold text-dark border-bottom pb-2">Informasi Nota & Pengiriman</h6>
 <table class="table table-sm table-borderless">
 <tr>
 <th width="40%">No Nota Penjualan</th>
 <td>: <a href="{{ route('admin.penjualan.show', $pengiriman->penjualan_id) }}" class="fw-bold" style="color: #2A9D8F;">{{ $pengiriman->penjualan->no_nota }}</a></td>
 </tr>
 <tr>
 <th>Tanggal & Waktu Berangkat</th>
 <td>: <strong>{{ $pengiriman->tanggal_kirim ? $pengiriman->tanggal_kirim->format('d F Y - H:i') : '-' }} WIB</strong></td>
 </tr>
 <tr>
 <th>Petugas Gudang</th>
 <td>: {{ $pengiriman->user->name ?? '-' }}</td>
 </tr>
 <tr>
 <th>Catatan Supir / Proyek</th>
 <td>: <em>{{ $pengiriman->catatan ?? '-' }}</em></td>
 </tr>
 </table>
 </div>
 <div class="col-md-6">
 <h6 class="fw-bold text-dark border-bottom pb-2">Informasi Penerima / Customer</h6>
 <table class="table table-sm table-borderless">
 <tr>
 <th width="35%">Nama Customer</th>
 <td>: <strong>{{ $pengiriman->penjualan->customer->nama_customer ?? '-' }}</strong></td>
 </tr>
 <tr>
 <th>No Telepon</th>
 <td>: {{ $pengiriman->penjualan->customer->no_telepon ?? '-' }}</td>
 </tr>
 <tr>
 <th>Alamat Kirim</th>
 <td>: {{ $pengiriman->penjualan->customer->alamat ?? '-' }}</td>
 </tr>
 </table>
 </div>
 </div>

 <h6 class="fw-bold text-dark mb-2">Rincian Barang yang Diangkut pada Surat Jalan Ini</h6>
 <div class="table-responsive">
 <table class="table table-hover table-striped align-middle">
 <thead class="table-light">
 <tr>
 <th width="5%" class="text-center">No</th>
 <th width="15%">Kode Produk</th>
 <th>Nama Produk</th>
 <th width="20%" class="text-center">Kuantitas Dikirim</th>
 </tr>
 </thead>
 <tbody>
 @foreach($pengiriman->detailPengiriman as $index => $item)
 <tr>
 <td class="text-center">{{ $index + 1 }}</td>
 <td><span class="badge" style="background-color: #F1F5F9; color: #475569; font-family: monospace;">{{ $item->detailPenjualan->produk->kode_produk ?? '-' }}</span></td>
 <td><strong>{{ $item->detailPenjualan->produk->nama_produk ?? '-' }}</strong></td>
 <td class="text-center">
 <span class="badge" style="background-color: #E0F2FE; color: #0369A1; font-size: 0.9rem; padding: 6px 12px;">
 {{ (float)$item->qty_dikirim }} {{ $item->detailPenjualan->produk->satuanDasar->nama_satuan ?? 'Item' }}
 </span>
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 </div>
 <div class="card-footer d-flex justify-content-between bg-white py-3 border-0">
 <a href="{{ route('admin.penjualan.pengiriman.index') }}" class="btn btn-sm" style="background-color: #E2E8F0; color: #334155; font-weight: 500; padding: 7px 16px;">
 <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat Pengiriman
 </a>
 </div>
 </div>
 </div>
</div>
@endsection