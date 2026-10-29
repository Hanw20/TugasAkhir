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
                <div class="card mb-3">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0"><i class="bi bi-box-arrow-in-down me-2"></i> Konfirmasi Barang Datang
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Nomor Faktur</label>
                                <input type="text" class="form-control" value="{{ $pembelian->no_faktur }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Supplier</label>
                                <input type="text" class="form-control"
                                    value="{{ $pembelian->supplier->nama_supplier ?? '-' }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tanggal Diterima Fisik</label>
                                <input type="datetime-local" name="tanggal_terima" class="form-control"
                                    value="{{ date('Y-m-d\TH:i') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tanggal Diterima Fisik</label>
                                <input type="datetime-local" name="tanggal_terima" class="form-control"
                                    value="{{ date('Y-m-d\TH:i') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-success">
                                    <i class="bi bi-star-fill text-warning me-1"></i> Penilaian Kualitas Fisik Produk (K2)
                                </label>
                                <select name="nilai_kualitas" class="form-select border-success" required>
                                    <option value="5">⭐⭐⭐⭐⭐ Sangat Baik (Kondisi 100% Mulus, Kemasan Utuh)</option>
                                    <option value="4">⭐⭐⭐⭐ Baik (Hanya lecet minor pada kemasan luar)</option>
                                    <option value="3">⭐⭐⭐ Cukup (Ada sedikit cacat/remuk tapi masih bisa dijual)</option>
                                    <option value="2">⭐⭐ Kurang (Banyak barang retak/rusak/basah)</option>
                                    <option value="1">⭐ Sangat Buruk (Kualitas barang rusak parah / tidak sesuai standar)
                                    </option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Catatan Petugas Gudang (Opsional)</label>
                                <input type="text" name="catatan" class="form-control"
                                    placeholder="Contoh: Barang tiba kondisi mulus tanpa cacat, supir Pak Anto">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Catatan Petugas Gudang (Opsional)</label>
                                <input type="text" name="catatan" class="form-control"
                                    placeholder="Contoh: Barang tiba kondisi mulus tanpa cacat, supir Pak Anto">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Tabel Checklist Barang Fisik -->
                <div class="card mb-3">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="card-title mb-0"><i class="bi bi-check2-square me-2"></i> Ceklist Kuantitas Fisik Barang
                        </h5>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-bordered mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Produk</th>
                                    <th width="15%" class="text-center">Qty Dipesan</th>
                                    <th width="15%" class="text-center">Sudah Diterima Lalu</th>
                                    <th width="15%" class="text-center">Sisa Belum Diterima</th>
                                    <th width="20%">Qty Diterima Hari Ini</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pembelian->detailPembelian as $index => $detail)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $detail->produk->nama_produk ?? '-' }}</strong><br>
                                            <small class="text-muted">{{ $detail->produk->kode_produk }}</small>
                                            <input type="hidden" name="detail_id[]" value="{{ $detail->id }}">
                                        </td>
                                        <td class="text-center fw-bold">
                                            {{ (int) $detail->qty }} {{ $detail->produk->satuanDasar->nama_satuan ?? '' }}
                                        </td>
                                        <td class="text-center text-success fw-bold">
                                            {{ (int) $detail->qty_diterima }}
                                        </td>
                                        <td class="text-center text-danger fw-bold">
                                            {{ (int) $detail->sisa_qty }}
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" name="qty_diterima[]" class="form-control fw-bold" min="0"
                                                    max="{{ (int) $detail->sisa_qty }}" value="{{ (int) $detail->sisa_qty }}"
                                                    required>
                                                <span
                                                    class="input-group-text">{{ $detail->produk->satuanDasar->nama_satuan ?? 'Item' }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <a href="{{ route('admin.pembelian.show', $pembelian->id) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-success px-4 fw-bold">
                            <i class="bi bi-check-circle"></i> Konfirmasi Barang Diterima & Masukkan ke Stok
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection