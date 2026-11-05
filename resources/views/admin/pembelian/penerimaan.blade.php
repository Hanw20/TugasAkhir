@extends('layouts.adminlte')

@section('title', 'Checklist Penerimaan Barang')
@section('page_title', 'Checklist Penerimaan Barang: Faktur #' . $pembelian->no_faktur)

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.pembelian.terima.store', $pembelian->id) }}" method="POST">
            @csrf

            <!-- Card Informasi Faktur & Penerimaan -->
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0"><i class="bi bi-box-arrow-in-down me-2"></i> Konfirmasi Barang Datang</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Nomor Faktur</label>
                            <input type="text" class="form-control bg-light" value="{{ $pembelian->no_faktur }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Supplier</label>
                            <input type="text" class="form-control bg-light" value="{{ $pembelian->supplier->nama_supplier ?? '-' }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tanggal Diterima Fisik</label>
                            <input type="datetime-local" name="tanggal_terima" class="form-control" value="{{ date('Y-m-d\TH:i') }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Catatan Petugas Gudang (Opsional)</label>
                            <input type="text" name="catatan" class="form-control" placeholder="Contoh: Barang tiba kondisi mulus, supir Pak Anto, bongkar muat selesai jam 15:00">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Tabel Checklist Barang Fisik & Penilaian Kualitas Per Produk -->
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0"><i class="bi bi-check2-square me-2"></i> Ceklist Kuantitas & Kualitas Fisik Per Produk</h5>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="4%">No</th>
                                <th width="28%">Nama Produk</th>
                                <th width="12%" class="text-center">Qty Dipesan</th>
                                <th width="12%" class="text-center">Sudah Terima</th>
                                <th width="12%" class="text-center">Sisa Belum</th>
                                <th width="14%">Qty Diterima Hari Ini</th>
                                <th width="18%">Penilaian Kualitas Produk (K2)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pembelian->detailPembelian as $index => $detail)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $detail->produk->nama_produk ?? '-' }}</strong><br>
                                        <small class="text-muted">{{ $detail->produk->kode_produk }}</small>
                                        <input type="hidden" name="detail_id[]" value="{{ $detail->id }}">
                                    </td>
                                    <td class="text-center fw-bold">
                                        {{ (float) $detail->qty }} {{ $detail->produk->satuanDasar->nama_satuan ?? '' }}
                                    </td>
                                    <td class="text-center text-primary fw-bold">
                                        {{ (float) $detail->qty_diterima }}
                                    </td>
                                    <td class="text-center text-danger fw-bold">
                                        {{ (float) $detail->sisa_qty }}
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" step="any" name="qty_diterima[]" class="form-control"
                                                value="{{ (float) $detail->sisa_qty }}" min="0" max="{{ (float) $detail->sisa_qty }}" required>
                                            <span class="input-group-text small">{{ $detail->produk->satuanDasar->nama_satuan ?? '' }}</span>
                                        </div>
                                    </td>
                                    <!-- PENILAIAN KUALITAS KHUSUS PER ITEM PRODUK -->
                                    <td>
                                        <select name="nilai_kualitas[]" class="form-select form-select-sm">
                                            <option value="5" selected>⭐ 5 - Sangat Baik (Mulus)</option>
                                            <option value="4">⭐ 4 - Baik (Lecet Minor)</option>
                                            <option value="3">⭐ 3 - Cukup (Ada sedikit cacat)</option>
                                            <option value="2">⭐ 2 - Kurang (Cacat/Rusak)</option>
                                            <option value="1">⭐ 1 - Sangat Buruk (Hancur)</option>
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex justify-content-between bg-light py-2">
                    <a href="{{ route('admin.pembelian.show', $pembelian->id) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-success px-4 fw-bold">
                        <i class="bi bi-box-seam me-1"></i> Konfirmasi Barang Diterima & Simpan Penilaian
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection