@extends('layouts.adminlte')

@section('title', 'Rekomendasi Supplier Terbaik')
@section('page_title', 'Rekomendasi Supplier Terbaik')

@section('content')
<div class="container-fluid px-3">
 <!-- Judul Halaman -->
 <div class="d-flex justify-content-between align-items-center mb-3">
 <div>
 <h4 class="mb-0 fw-bold" style="color: #264653;">Rekomendasi Supplier Terbaik</h4>
 <p class="text-muted small mb-0">Penentuan supplier optimal menggunakan integrasi pembobotan AHP dan perankingan SAW</p>
 </div>
 @if ($selectedProduk)
 <a href="{{ route('admin.rekomendasi-supplier.show', ['id' => $selectedProduk->id, 'sesi_id' => $sesiDipilih->id ?? '']) }}" 
 class="btn btn-sm btn-outline-secondary" >
 <i class="bi bi-calculator me-1 text-primary"></i> Lihat Rincian Perhitungan Matematis &rarr;
 </a>
 @endif
 </div>

 <!-- 1. CARD PARAMETER REKOMENDASI SUPPLIER -->
 <div class="card mb-4" >
 <div class="card-header py-2 text-white" style="background-color: #264653; border-top-left-radius: 8px; border-top-right-radius: 8px;">
 <h6 class="mb-0 fw-semibold"><i class="bi bi-sliders me-2"></i>Parameter Rekomendasi Supplier</h6>
 </div>
 <div class="card-body p-3">
 <form method="GET" action="{{ route('admin.rekomendasi-supplier.index') }}">
 <div class="row g-3 align-items-end">
 <div class="col-md-5">
 <label class="form-label small fw-semibold text-muted mb-1">Pilih Produk yang Ingin Direstock</label>
 <select name="produk_id" class="form-select form-select-sm" required onchange="this.form.submit()">
 <option value="">-- Pilih Produk untuk Dianalisis --</option>
 @foreach ($daftarProduk as $p)
 <option value="{{ $p->id }}" {{ request('produk_id') == $p->id ? 'selected' : '' }}>
 [{{ $p->kode_produk }}] {{ $p->nama_produk }}
 </option>
 @endforeach
 </select>
 </div>
 <div class="col-md-4">
 <label class="form-label small fw-semibold text-muted mb-1">Pilih Sesi Pembobotan Kriteria AHP</label>
 <select name="sesi_id" class="form-select form-select-sm" onchange="this.form.submit()">
 @foreach ($daftarSesiAhp as $s)
 <option value="{{ $s->id }}" {{ ($sesiDipilih && $sesiDipilih->id == $s->id) ? 'selected' : '' }}>
 {{ $s->nama_sesi }} ({{ \Carbon\Carbon::parse($s->tanggal_hitung)->format('d/m/Y') }})
 </option>
 @endforeach
 </select>
 </div>
 <div class="col-md-3 d-flex gap-2">
 <a href="{{ route('admin.sesi-perhitungan-ahp.index') }}" class="btn btn-sm btn-outline-success w-50">
 <i class="bi bi-plus-circle me-1"></i> Sesi Baru
 </a>
 <button type="submit" class="btn btn-sm text-white w-50" style="background-color: #2A9D8F;">
 <i class="bi bi-funnel me-1"></i> Analisis
 </button>
 </div>
 </div>
 </form>
 </div>
 </div>

 @if ($selectedProduk)
 @if (count($ranking) > 0)
 <!-- 2. BARIS TENGAH: PRODUK DIANALISIS & DAFTAR REKOMENDASI UTAMA -->
 <div class="row g-3 mb-4">
 <!-- Kolom Kiri: Produk yang Dianalisis -->
 <div class="col-md-5">
 <div class="card h-100" >
 <div class="card-header py-2 text-white" style="background-color: #264653; border-top-left-radius: 8px; border-top-right-radius: 8px;">
 <h6 class="mb-0 fw-semibold"><i class="bi bi-box-seam me-2"></i>Produk yang Dianalisis</h6>
 </div>
 <div class="card-body d-flex flex-column justify-content-between p-3" style="background-color: #F8FBF9;">
 <div>
 <h5 class="fw-bold mb-2" style="color: #2A9D8F;">{{ $selectedProduk->nama_produk }}</h5>
 <div class="text-muted small mb-1">
 <strong>Kode:</strong> {{ $selectedProduk->kode_produk }}
 </div>
 <div class="text-muted small mb-3">
 <strong>Kategori:</strong> {{ $selectedProduk->kategori->nama_kategori ?? '-' }}
 </div>
 </div>
 <div class="pt-3 border-top d-flex justify-content-between align-items-center">
 <span class="text-muted small fw-semibold">Stok Fisik Gudang:</span>
 <span class="badge px-3 py-2 text-white" style="background-color: #2A9D8F; font-size: 0.85rem;">
 {{ $selectedProduk->stok }} {{ $selectedProduk->satuanDasar->nama_satuan ?? 'Unit' }}
 </span>
 </div>
 </div>
 </div>
 </div>

 <!-- Kolom Kanan: Daftar Rekomendasi Supplier Utama -->
 <div class="col-md-7">
 <div class="card h-100" >
 <div class="card-header py-2 text-white d-flex justify-content-between align-items-center" style="background-color: #264653; border-top-left-radius: 8px; border-top-right-radius: 8px;">
 <h6 class="mb-0 fw-semibold"><i class="bi bi-trophy me-2"></i>Hasil Rekomendasi Utama (AHP-SAW)</h6>
 <span class="badge bg-light text-dark small" style="font-size: 0.75rem;">Metode Utama</span>
 </div>
 <div class="table-responsive">
 <table class="table table-hover align-middle mb-0">
 <thead style="background-color: #F4F6F5;">
 <tr>
 <th class="text-center" style="width: 100px;">Rangking</th>
 <th>Kode & Nama Supplier</th>
 <th>Nomor Telepon</th>
 <th class="text-center" style="width: 130px;">Aksi</th>
 </tr>
 </thead>
 <tbody>
 @foreach ($ranking as $index => $item)
 <tr class="{{ $index == 0 ? 'table-success bg-opacity-25' : '' }}">
 <td class="text-center">
 <span class="badge px-2 py-1" style="{{ $index == 0 ? 'background-color: #D1FAE5; color: #065F46;' : ($index == 1 ? 'background-color: #E0F2FE; color: #0369A1;' : 'background-color: #F1F5F9; color: #475569;') }} font-size: 0.85rem;">
 <i class="bi bi-trophy-fill me-1"></i>#{{ $index + 1 }}
 </span>
 </td>
 <td>
 <div class="fw-bold text-dark">{{ $item['supplier']->nama_supplier }}</div>
 <small class="text-muted">({{ $item['supplier']->kode_supplier ?? 'SUP-'.str_pad($item['supplier']->id, 3, '0', STR_PAD_LEFT) }})</small>
 </td>
 <td>
 <i class="bi bi-telephone text-muted me-1"></i>
 {{ $item['supplier']->telepon ?? '-' }}
 </td>
 <td class="text-center">
 <a href="{{ route('admin.pembelian.create', ['supplier_id' => $item['supplier']->id, 'produk_id' => $selectedProduk->id]) }}" 
 class="btn btn-sm py-1 px-2" style="{{ $index == 0 ? 'background-color: #2A9D8F; color: #ffffff;' : 'background-color: #E0F2FE; color: #0369A1;' }} font-size: 0.8rem; font-weight: 500;">
 <i class="bi bi-cart-plus me-1"></i> Buat PO
 </a>
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 </div>
 </div>
 </div>

 <!-- 3. BAGIAN BAWAH: TABEL KOMPARASI 3 METODE BERDAMPINGAN -->
 <div class="card mb-4" >
 <div class="card-header py-2 text-white" style="background-color: #264653; border-top-left-radius: 8px; border-top-right-radius: 8px;">
 <h6 class="mb-0 fw-semibold"><i class="bi bi-arrow-left-right me-2"></i>Komparasi Hasil Rekomendasi Antar Metode SPK</h6>
 </div>
 <div class="table-responsive">
 <table class="table table-bordered table-hover align-middle mb-0 text-center">
 <thead style="background-color: #F4F6F5;">
 <tr>
 <th style="width: 100px;">Peringkat</th>
 <th style="background-color: #E8F5E9; color: #2E7D32;">
 AHP-SAW (Metode Utama)
 </th>
 <th style="background-color: #E3F2FD; color: #1565C0;">
 SAW (Tunggal / Equal Weight)
 </th>
 <th style="background-color: #FFF3E0; color: #E65100;">
 AHP (Tunggal)
 </th>
 </tr>
 </thead>
 <tbody>
 @php
 $maxRows = max(count($ranking), count($rankingSawMurni), count($rankingAhpMurni));
 @endphp
 @for ($i = 0; $i < $maxRows; $i++)
 <tr>
 <td class="fw-bold">
 <span class="badge {{ $i == 0 ? 'bg-success' : 'bg-light text-muted border' }}">
 <i class="bi bi-trophy-fill me-1"></i>#{{ $i + 1 }}
 </span>
 </td>
 <!-- AHP-SAW -->
 <td class="text-start" style="background-color: #F9FFF9;">
 @if (isset($ranking[$i]))
 <div class="fw-bold text-dark">{{ $ranking[$i]['supplier']->nama_supplier }}</div>
 <small class="text-muted">({{ $ranking[$i]['supplier']->kode_supplier ?? 'SUP-'.str_pad($ranking[$i]['supplier']->id, 3, '0', STR_PAD_LEFT) }})</small>
 <span class="badge bg-success float-end mt-1">{{ number_format($ranking[$i]['skor_preferensi'], 4) }}</span>
 @else
 <span class="text-muted">-</span>
 @endif
 </td>
 <!-- SAW TUNGGAL -->
 <td class="text-start" style="background-color: #F8FBFF;">
 @if (isset($rankingSawMurni[$i]))
 <div class="fw-bold text-dark">{{ $rankingSawMurni[$i]['supplier']->nama_supplier }}</div>
 <small class="text-muted">({{ $rankingSawMurni[$i]['supplier']->kode_supplier ?? 'SUP-'.str_pad($rankingSawMurni[$i]['supplier']->id, 3, '0', STR_PAD_LEFT) }})</small>
 <span class="badge float-end mt-1" style="background-color: #E0F2FE; color: #0369A1;">{{ number_format($rankingSawMurni[$i]['skor_preferensi'], 4) }}</span>
 @else
 <span class="text-muted">-</span>
 @endif
 </td>
 <!-- AHP TUNGGAL -->
 <td class="text-start" style="background-color: #FFFAF5;">
 @if (isset($rankingAhpMurni[$i]))
 <div class="fw-bold text-dark">{{ $rankingAhpMurni[$i]['supplier']->nama_supplier }}</div>
 <small class="text-muted">({{ $rankingAhpMurni[$i]['supplier']->kode_supplier ?? 'SUP-'.str_pad($rankingAhpMurni[$i]['supplier']->id, 3, '0', STR_PAD_LEFT) }})</small>
 <span class="badge bg-warning text-dark float-end mt-1">{{ number_format($rankingAhpMurni[$i]['skor_preferensi'], 4) }}</span>
 @else
 <span class="text-muted">-</span>
 @endif
 </td>
 </tr>
 @endfor
 </tbody>
 </table>
 </div>
 </div>
 @else
 <div class="card p-5 text-center my-4" >
 <i class="bi bi-info-circle text-muted fs-1 mb-3"></i>
 <h5 class="fw-bold text-muted">Belum Ada Supplier Terhubung</h5>
 <p class="text-muted small">Hubungkan produk ini dengan minimal 2 supplier pada menu Master Supplier untuk memulai kalkulasi.</p>
 </div>
 @endif
 @else
 <div class="card p-5 text-center my-4" >
 <i class="bi bi-boxes text-muted fs-1 mb-3"></i>
 <h5 class="fw-bold text-muted">Silakan Pilih Produk Terlebih Dahulu</h5>
 <p class="text-muted small">Pilih salah satu produk bahan bangunan pada parameter di atas untuk melihat rekomendasi supplier terbaik.</p>
 </div>
 @endif
</div>
@endsection