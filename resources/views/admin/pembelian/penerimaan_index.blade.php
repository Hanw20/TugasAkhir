@extends('layouts.adminlte')

@section('title', 'Riwayat Penerimaan Barang')
@section('page_title', 'Riwayat Penerimaan Fisik Barang (Gudang)')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0"><i class="bi bi-box-seam me-2"></i> Daftar Log Kedatangan Barang Gudang</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>No Faktur Pembelian</th>
                            <th>Tanggal & Waktu Masuk Fisik</th>
                            <th>Supplier</th>
                            <th>Petugas Gudang</th>
                            <th>Catatan Supir / Fisik</th>
                            <th width="14%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penerimaan as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><span class="badge bg-secondary fs-6">{{ $row->pembelian->no_faktur ?? '-' }}</span></td>
                            <td>{{ $row->tanggal_terima ? $row->tanggal_terima->format('d/m/Y H:i') : '-' }} WIB</td>
                            <td><strong>{{ $row->pembelian->supplier->nama_supplier ?? '-' }}</strong></td>
                            <td>{{ $row->user->name ?? '-' }}</td>
                            <td>{{ $row->catatan ?? '-' }}</td>
                            <td class="text-center">
                                <!-- Mengarah ke Bukti Penerimaan Fisik, BUKAN faktur -->
                                <a href="{{ route('admin.pembelian.penerimaan.show', $row->id) }}" class="btn btn-sm btn-info text-white fw-bold">
                                    <i class="bi bi-eye"></i> Detail Barang
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <em>Belum ada riwayat penerimaan barang fisik.</em>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection