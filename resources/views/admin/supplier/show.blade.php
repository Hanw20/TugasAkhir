@extends('layouts.adminlte')

@section('title', 'Detail Supplier')
@section('page_title', 'Detail Supplier: ' . $supplier->nama_supplier)

@section('content')
    <div class="row">
        <!-- CARD INFORMASI SUPPLIER (FULL WIDTH) -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-truck me-2"></i> Informasi Supplier</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0 align-middle">
                            <tbody>
                                <tr>
                                    <th width="20%" class="ps-3">Kode Supplier</th>
                                    <td><span class="badge bg-secondary font-monospace fs-6">{{ $supplier->kode_supplier }}</span></td>
                                </tr>
                                <tr>
                                    <th class="ps-3">Nama Supplier / PT</th>
                                    <td class="fw-bold fs-6">{{ $supplier->nama_supplier }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-3">Nomor Telepon</th>
                                    <td>{{ $supplier->nomor_telepon ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-3">Email</th>
                                    <td>{{ $supplier->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-3">Alamat</th>
                                    <td>{{ $supplier->alamat ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between bg-light py-2">
                    <a href="{{ route('admin.supplier.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Supplier
                    </a>
                     <button type="button" class="btn btn-warning text-dark btn-sm fw-bold shadow-sm" data-bs-toggle="modal"
                        data-bs-target="#modalBeriNilai">
                        <i class="bi bi-star-fill me-1 text-dark"></i> + Beri Nilai Kinerja (K3 & K4)
                    </button>
                </div>
            </div>
        </div>

        <!-- CARD DAFTAR PRODUK YANG DIPASOK (FULL WIDTH) -->
        <div class="col-12">
            <div class="card card-outline card-success shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold m-0"><i class="bi bi-box-seam me-2"></i> Daftar Produk yang Dipasok</h5>
                    <button type="button" class="btn btn-sm btn-success fw-bold" data-bs-toggle="modal"
                        data-bs-target="#modalTambahProdukSupplier">
                        <i class="bi bi-plus-circle me-1"></i> Hubungkan Produk
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle m-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;" class="text-center">No</th>
                                    <th>Kode Produk</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori / Merek</th>
                                    <th>Satuan</th>
                                    <th class="text-center" style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($supplier->produk as $idx => $p)
                                    <tr>
                                        <td class="text-center">{{ $idx + 1 }}</td>
                                        <td><span class="badge bg-secondary font-monospace">{{ $p->kode_produk }}</span></td>
                                        <td class="fw-bold">{{ $p->nama_produk }}</td>
                                        <td>{{ $p->kategori->nama_kategori ?? '-' }} / {{ $p->merek->nama_merek ?? '-' }}</td>
                                        <td>{{ $p->satuan->nama_satuan ?? '-' }}</td>
                                        <td class="text-center">
                                            <form
                                                action="{{ route('admin.supplier.produk.detach', ['id' => $supplier->id, 'produk_id' => $p->id]) }}"
                                                method="POST"
                                                onsubmit="return confirm('Hapus produk ini dari daftar pasokan supplier?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus dari daftar">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="bi bi-info-circle fs-4 d-block mb-2"></i>
                                            Belum ada produk yang dihubungkan ke supplier ini. Silakan klik tombol <b>Hubungkan Produk</b> di atas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL HUBUNGKAN PRODUK -->
    <div class="modal fade" id="modalTambahProdukSupplier" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.supplier.produk.attach', $supplier->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title"><i class="bi bi-link-45deg me-2"></i> Hubungkan Produk ke Supplier</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Produk</label>
                            <select name="produk_id" class="form-select" required>
                                <option value="">-- Pilih Produk --</option>
                                @foreach(\App\Models\Produk::orderBy('nama_produk')->get() as $prod)
                                    <option value="{{ $prod->id }}">{{ $prod->kode_produk }} - {{ $prod->nama_produk }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-save me-1"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EVALUASI KINERJA SUPPLIER (K3 & K4) -->
    <div class="modal fade" id="modalBeriNilai" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.supplier.nilai.store', $supplier->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title fw-bold"><i class="bi bi-star-fill me-2"></i> Evaluasi Kinerja Supplier (K3 & K4)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Supplier</label>
                            <input type="text" class="form-control bg-light" value="{{ $supplier->nama_supplier }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanggal Evaluasi</label>
                            <input type="date" name="tanggal_penilaian" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <!-- 1. Kecepatan Respon (K3) -->
                        <div class="mb-3 p-3 border rounded border-primary bg-light">
                            <label class="form-label fw-bold text-primary mb-1">
                                <i class="bi bi-chat-dots-fill me-1"></i> 1. Kecepatan Respon / Komunikasi (K3)
                            </label>
                            <small class="text-muted d-block mb-2">Kecepatan membalas pesan, konfirmasi pesanan, dan info stok.</small>
                            <select name="nilai_respon" class="form-select border-primary fw-bold" required>
                                <option value="5">★★★★★ 5 - Sangat Cepat (Respon < 15 menit, sales sangat aktif)</option>
                                <option value="4" selected>★★★★☆ 4 - Cepat (Respon dalam hitungan jam)</option>
                                <option value="3">★★★☆☆ 3 - Cukup (Respon standar jam kerja)</option>
                                <option value="2">★★☆☆☆ 2 - Lambat (Sering slow respon)</option>
                                <option value="1">★☆☆☆☆ 1 - Sangat Lambat (Sulit dihubungi)</option>
                            </select>
                        </div>

                        <!-- 2. Kebijakan Garansi & Klaim (K4) -->
                        <div class="mb-3 p-3 border rounded border-success bg-light">
                            <label class="form-label fw-bold text-success mb-1">
                                <i class="bi bi-shield-check me-1"></i> 2. Kebijakan Garansi & Klaim Retur (K4)
                            </label>
                            <small class="text-muted d-block mb-2">Kemudahan retur barang rusak/cacat dan kecepatan proses ganti rugi.</small>
                            <select name="nilai_garansi" class="form-select border-success fw-bold" required>
                                <option value="5">★★★★★ 5 - Sangat Mudah (Barang rusak langsung diganti baru)</option>
                                <option value="4" selected>★★★★☆ 4 - Mudah (Garansi resmi, proses klaim wajar)</option>
                                <option value="3">★★★☆☆ 3 - Cukup (Bisa retur tapi butuh waktu pemeriksaan lama)</option>
                                <option value="2">★★☆☆☆ 2 - Sulit (Klaim berbelit-belit, sering tolak retur)</option>
                                <option value="1">★☆☆☆☆ 1 - Tidak Ada Garansi (Resiko ditanggung toko)</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-bold">Catatan Evaluasi Owner (Opsional)</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Contoh: Sales ramah, tapi proses retur semen bocor butuh approval 2 hari."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning fw-bold px-4">
                            <i class="bi bi-save me-1"></i> Simpan Evaluasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection