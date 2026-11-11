@extends('layouts.adminlte')

@section('title', 'Checklist Pengiriman Barang')
@section('page_title', 'Checklist Pengiriman Barang: Nota #' . $penjualan->no_nota)

@section('content')
<div class="row">
 <div class="col-12">
 @if(session('error'))
 <div class="alert alert-danger">{{ session('error') }}</div>
 @endif

 <form action="{{ route('admin.penjualan.kirim.store', $penjualan->id) }}" method="POST">
 @csrf

 <!-- Card Informasi Nota & Pengiriman -->
 <div class="card mb-3" >
 <div class="card-header text-white" style="background-color: #264653;">
 <h5 class="card-title mb-0 fw-bold"><i class="bi bi-truck me-2"></i> Konfirmasi Barang Diberangkatkan</h5>
 </div>
 <div class="card-body">
 <div class="row g-3">
 <div class="col-md-4">
 <label class="form-label fw-bold">Nomor Nota Penjualan</label>
 <input type="text" class="form-control" value="{{ $penjualan->no_nota }}" readonly>
 </div>
 <div class="col-md-4">
 <label class="form-label fw-bold">Customer</label>
 <input type="text" class="form-control" value="{{ $penjualan->customer->nama_customer ?? '-' }}" readonly>
 </div>
 <div class="col-md-4">
 <label class="form-label fw-bold">Tanggal Berangkat Fisik</label>
 <input type="datetime-local" name="tanggal_kirim" class="form-control" value="{{ date('Y-m-d\TH:i') }}" required>
 </div>
 <div class="col-12">
 <label class="form-label fw-bold">Catatan Pengiriman / Supir</label>
 <input type="text" name="catatan" class="form-control" placeholder="Contoh: Dikirim via Pickup Toko L300 (Supir: Pak Wawan), kirim ke alamat proyek">
 </div>
 </div>
 </div>
 </div>

 <!-- Card Tabel Checklist Barang Fisik Naik Truk -->
 <div class="card mb-3">
 <div class="card-header bg-secondary text-white">
 <h5 class="card-title mb-0"><i class="bi bi-check2-square me-2"></i> Checklist Kuantitas Fisik Barang Keluar</h5>
 </div>
 <div class="card-body table-responsive p-0">
 <table class="table table-bordered mb-0 align-middle">
 <thead class="table-light">
 <tr>
 <th width="5%" class="text-center">No</th>
 <th>Nama Produk</th>
 <th width="15%" class="text-center">Qty Dipesan</th>
 <th width="15%" class="text-center">Sudah Dikirim Lalu</th>
 <th width="15%" class="text-center">Sisa Belum Dikirim</th>
 <th width="20%">Qty Dikirim Hari Ini</th>
 </tr>
 </thead>
 <tbody>
 @foreach($penjualan->detailPenjualan as $index => $detail)
 <tr>
 <td class="text-center">{{ $index + 1 }}</td>
 <td>
 <strong>{{ $detail->produk->nama_produk ?? '-' }}</strong><br>
 <small class="text-muted">{{ $detail->produk->kode_produk }}</small>
 <input type="hidden" name="detail_id[]" value="{{ $detail->id }}">
 </td>
 <td class="text-center fw-bold">
 {{ (float)$detail->qty }} {{ $detail->produk->satuanDasar->nama_satuan ?? '' }}
 </td>
 <td class="text-center text-primary fw-bold">
 {{ (float)$detail->qty_dikirim }}
 </td>
 <td class="text-center text-danger fw-bold">
 {{ (float)$detail->sisa_qty }}
 </td>
 <td>
 <div class="input-group">
 <input type="number" name="qty_dikirim[]" class="form-control fw-bold" 
 min="0" max="{{ (float)$detail->sisa_qty }}" value="{{ (float)$detail->sisa_qty }}" required>
 <span class="input-group-text">{{ $detail->produk->satuanDasar->nama_satuan ?? 'Item' }}</span>
 </div>
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 <div class="card-footer d-flex justify-content-between bg-white py-3 border-0">
 <a href="{{ route('admin.penjualan.show', $penjualan->id) }}" class="btn btn-sm" style="background-color: #E2E8F0; color: #334155; font-weight: 500; padding: 7px 16px;">
 <i class="bi bi-arrow-left me-1"></i> Batal
 </a>
 <button type="submit" class="btn btn-sm text-white px-4 fw-bold" style="background-color: #2A9D8F; padding: 7px 18px;">
 <i class="bi bi-truck me-1"></i> Konfirmasi Barang Dikirim & Potong Stok
 </button>
 </div>
 </div>
 </form>
 </div>
</div>
@endsection