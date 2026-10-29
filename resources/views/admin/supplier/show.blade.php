@extends('layouts.adminlte')

@section('title', 'Detail Supplier')
@section('page_title', 'Detail Supplier: ' . $supplier->nama_supplier)

@section('content')
<div class="row">
    <!-- Kolom Kiri: Profil & Kontak Supplier -->
    <div class="col-md-5 mb-3">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0"><i class="bi bi-truck me-2"></i> Informasi Supplier</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <tr>
                        <th width="40%">Kode Supplier</th>
                        <td><span class="badge bg-secondary fs-6">{{ $supplier->kode_supplier }}</span></td>
                    </tr>
                    <tr>
                        <th>Nama Supplier / PT</th>
                        <td><strong>{{ $supplier->nama_supplier }}</strong></td>
                    </tr>
                    <tr>
                        <th>Nomor Telepon</th>
                        <td>{{ $supplier->nomor_telepon ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $supplier->email ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $supplier->alamat ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('admin.supplier.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button type="button" class="btn btn-warning text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#modalBeriNilai">
                    <i class="bi bi-star-fill me-1 text-warning"></i> + Beri Nilai Pelayanan
                </button>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Riwayat Penilaian Kinerja Supplier (AHP / SAW) -->
    <div class="col-md-7 mb-3">
        <div class="card">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-award me-2"></i> Riwayat Skor Evaluasi SPK (AHP & SAW)
                </h5>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-bordered table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="20%">Tanggal</th>
                            <th width="25%">Kriteria</th>
                            <th width="15%" class="text-center">Skor (1-5)</th>
                            <th>Keterangan / Sumber</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supplier->riwayatPenilaian()->with('kriteria')->latest('tanggal_penilaian')->get() as $index => $rp)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $rp->tanggal_penilaian ? $rp->tanggal_penilaian->format('d/m/Y') : '-' }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $rp->kriteria->kode_kriteria ?? '-' }}</span>
                                <strong>{{ $rp->kriteria->nama_kriteria ?? '-' }}</strong>
                            </td>
                            <td class="text-center fw-bold text-success fs-6">
                                ⭐ {{ (float)$rp->nilai }}
                            </td>
                            <td>
                                <small>{{ $rp->keterangan ?? '-' }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                <em>Belum ada catatan penilaian fisik atau pelayanan untuk supplier ini.</em>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Beri Penilaian Pelayanan Supplier (K3) -->
<div class="modal fade" id="modalBeriNilai" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.supplier.nilai.store', $supplier->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="bi bi-star-fill me-2"></i> Evaluasi Pelayanan Supplier (K3)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Supplier</label>
                        <input type="text" class="form-control" value="{{ $supplier->nama_supplier }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal Evaluasi</label>
                        <input type="date" name="tanggal_penilaian" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tingkat Pelayanan & Responsivitas (1 - 5)</label>
                        <select name="nilai_pelayanan" class="form-select border-warning" required>
                            <option value="5">⭐⭐⭐⭐⭐ Sangat Memuaskan (Respon kilat, sales ramah, retur mudah)</option>
                            <option value="4">⭐⭐⭐⭐ Puas (Respon cepat dan kooperatif)</option>
                            <option value="3">⭐⭐⭐ Cukup (Respon standar, kadang slow respon)</option>
                            <option value="2">⭐⭐ Kurang (Slow respon, komplain berbelit-belit)</option>
                            <option value="1">⭐ Buruk Sekali (Tidak responsif, pelayanan mengecewakan)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan Evaluasi Owner</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Sales Pak Roni sangat membantu saat butuh pasokan darurat"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold">Simpan Evaluasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection