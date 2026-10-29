@extends('layouts.adminlte')

@section('title', 'Piutang Pelanggan')
@section('page_title', 'Manajemen Piutang Pelanggan & Tempo Proyek')

@section('content')
<div class="row">
    <!-- Tabel Tagihan Piutang yang Belum Lunas -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0"><i class="bi bi-exclamation-octagon me-2"></i> Daftar Nota Penjualan Belum Lunas (Piutang Customer)</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th>No Nota</th>
                            <th>Customer / Pelanggan</th>
                            <th>Tanggal Transaksi</th>
                            <th>Total Belanja</th>
                            <th>Sudah Dibayar</th>
                            <th>Sisa Piutang</th>
                            <th width="12%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notaPiutang as $index => $f)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td><span class="badge bg-secondary fs-6">{{ $f->no_nota }}</span></td>
                            <td><strong>{{ $f->customer->nama_customer ?? '-' }}</strong></td>
                            <td>{{ $f->tanggal ? $f->tanggal->format('d/m/Y') : '-' }}</td>
                            <td>Rp {{ number_format($f->total, 0, ',', '.') }}</td>
                            <td class="text-success">Rp {{ number_format($f->total_dibayar, 0, ',', '.') }}</td>
                            <td class="text-danger fw-bold">Rp {{ number_format($f->sisa_piutang, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-success fw-bold" 
                                        data-bs-toggle="modal" data-bs-target="#modalBayarPiutang"
                                        onclick="bukaModalPiutang({{ $f->id }}, '{{ $f->no_nota }}', {{ (int)$f->sisa_piutang }})">
                                    <i class="bi bi-cash-stack"></i> Terima Bayar
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-success py-4">
                                <i class="bi bi-check-circle fs-3 d-block mb-1"></i>
                                <strong>Semua piutang pelanggan sudah lunas!</strong>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tabel Riwayat Pembayaran Piutang -->
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="card-title mb-0"><i class="bi bi-clock-history me-2"></i> Riwayat Penerimaan Cicilan Piutang Customer</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th>No Nota</th>
                            <th>Customer</th>
                            <th>Tanggal Bayar</th>
                            <th>Jumlah Bayar</th>
                            <th>Kasir / Petugas</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayatBayar as $index => $rb)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $rb->penjualan->no_nota ?? '-' }}</td>
                            <td><strong>{{ $rb->penjualan->customer->nama_customer ?? '-' }}</strong></td>
                            <td>{{ $rb->tanggal_bayar ? $rb->tanggal_bayar->format('d/m/Y H:i') : '-' }}</td>
                            <td class="text-success fw-bold">Rp {{ number_format($rb->jumlah_bayar, 0, ',', '.') }}</td>
                            <td>{{ $rb->user->name ?? '-' }}</td>
                            <td>{{ $rb->catatan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">
                                <em>Belum ada catatan penerimaan pembayaran piutang.</em>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Terima Bayar Piutang Customer -->
<div class="modal fade" id="modalBayarPiutang" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.piutang-customer.store') }}" method="POST">
                @csrf
                <input type="hidden" name="penjualan_id" id="modal_penjualan_id">

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-cash-stack me-2"></i> Terima Pembayaran Piutang Customer</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nomor Nota</label>
                        <input type="text" id="modal_no_nota" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Sisa Piutang</label>
                        <input type="text" id="modal_sisa_piutang" class="form-control text-danger fw-bold" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal Pembayaran</label>
                        <input type="datetime-local" name="tanggal_bayar" class="form-control" value="{{ date('Y-m-d\TH:i') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jumlah yang Dibayar Customer (Rp)</label>
                        <input type="number" name="jumlah_bayar" id="modal_jumlah_bayar" class="form-control fw-bold" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan / Bukti Bayar</label>
                        <input type="text" name="catatan" class="form-control" placeholder="Contoh: Tunai kasir / Transfer BCA">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-bold">Simpan Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function bukaModalPiutang(id, noNota, sisaPiutang) {
        document.getElementById('modal_penjualan_id').value = id;
        document.getElementById('modal_no_nota').value = noNota;
        document.getElementById('modal_sisa_piutang').value = 'Rp ' + new Intl.NumberFormat('id-ID').format(sisaPiutang);
        document.getElementById('modal_jumlah_bayar').value = sisaPiutang;
        document.getElementById('modal_jumlah_bayar').max = sisaPiutang;
    }
</script>
@endpush