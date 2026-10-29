@extends('layouts.adminlte')

@section('title', 'Pembayaran Hutang Supplier')
@section('page_title', 'Manajemen Hutang Usaha & Tempo Supplier')

@section('content')
    <div class="row">
        <!-- Tabel Tagihan Hutang yang Belum Lunas -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0"><i class="bi bi-exclamation-octagon me-2"></i> Daftar Tagihan Faktur Belum
                        Lunas (Tempo)</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>No Faktur</th>
                                <th>Supplier</th>
                                <th>Jatuh Tempo</th>
                                <th>Total Tagihan</th>
                                <th>Sudah Dibayar</th>
                                <th>Sisa Hutang</th>
                                <th width="12%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fakturHutang as $index => $f)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><span class="badge bg-secondary">{{ $f->no_faktur }}</span></td>
                                    <td><strong>{{ $f->supplier->nama_supplier ?? '-' }}</strong></td>
                                    <td>
                                        @if($f->tanggal_jatuh_tempo)
                                            <span
                                                class="badge {{ $f->tanggal_jatuh_tempo < now() ? 'bg-danger' : 'bg-warning text-dark' }}">
                                                {{ $f->tanggal_jatuh_tempo->format('d/m/Y') }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-dark">Non-Tempo</span>
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($f->total, 0, ',', '.') }}</td>
                                    <td class="text-success">Rp {{ number_format($f->total_dibayar, 0, ',', '.') }}</td>
                                    <td class="text-danger fw-bold">Rp {{ number_format($f->sisa_hutang, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-success fw-bold" data-bs-toggle="modal"
                                            data-bs-target="#modalBayarHutang"
                                            onclick="bukaModalBayar({{ $f->id }}, '{{ $f->no_faktur }}', {{ (int) $f->sisa_hutang }})">
                                            <i class="bi bi-cash-stack"></i> Bayar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-success py-4">
                                        <i class="bi bi-check-circle fs-3 d-block mb-1"></i>
                                        <strong>Semua hutang faktur pembelian sudah lunas!</strong>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tabel Riwayat Pembayaran Hutang -->
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0"><i class="bi bi-clock-history me-2"></i> Riwayat Cicilan / Pembayaran Hutang
                    </h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>No Faktur</th>
                                <th>Supplier</th>
                                <th>Tanggal Bayar</th>
                                <th>Jumlah Bayar</th>
                                <th>Petugas</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayatBayar as $index => $rb)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $rb->pembelian->no_faktur ?? '-' }}</td>
                                    <td><strong>{{ $rb->pembelian->supplier->nama_supplier ?? '-' }}</strong></td>
                                    <td>{{ $rb->tanggal_bayar ? $rb->tanggal_bayar->format('d/m/Y H:i') : '-' }}</td>
                                    <td class="text-success fw-bold">Rp {{ number_format($rb->jumlah_bayar, 0, ',', '.') }}</td>
                                    <td>{{ $rb->user->name ?? '-' }}</td>
                                    <td>{{ $rb->catatan ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">
                                        <em>Belum ada catatan pembayaran hutang.</em>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Bayar Hutang -->
    <div class="modal fade" id="modalBayarHutang" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.pembayaran-hutang.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="pembelian_id" id="modal_pembelian_id">

                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title"><i class="bi bi-cash-stack me-2"></i> Pembayaran Hutang Supplier</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nomor Faktur</label>
                            <input type="text" id="modal_no_faktur" class="form-control" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Sisa Hutang Tagihan</label>
                            <input type="text" id="modal_sisa_hutang" class="form-control text-danger fw-bold" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanggal Pembayaran</label>
                            <input type="datetime-local" name="tanggal_bayar" class="form-control"
                                value="{{ date('Y-m-d\TH:i') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Jumlah yang Dibayar (Rp)</label>
                            <input type="number" name="jumlah_bayar" id="modal_jumlah_bayar" class="form-control fw-bold"
                                required min="1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Catatan / Bukti Transfer</label>
                            <input type="text" name="catatan" class="form-control"
                                placeholder="Contoh: Transfer via BCA / Tunai kas toko">
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
    function bukaModalBayar(id, noFaktur, sisaHutang) {
        document.getElementById('modal_pembelian_id').value = id;
        document.getElementById('modal_no_faktur').value = noFaktur;
        document.getElementById('modal_sisa_hutang').value = 'Rp ' + new Intl.NumberFormat('id-ID').format(sisaHutang);
        document.getElementById('modal_jumlah_bayar').value = sisaHutang;
        document.getElementById('modal_jumlah_bayar').max = sisaHutang;
    }
</script>
@endpush