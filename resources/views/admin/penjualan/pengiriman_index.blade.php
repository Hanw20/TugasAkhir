@extends('layouts.adminlte')

@section('title', 'Riwayat Pengiriman Barang')
@section('page_title', 'Riwayat Pengiriman Barang Keluar (Gudang)')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0"><i class="bi bi-truck me-2"></i> Daftar Log Surat Jalan Pengiriman</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th>No Nota Penjualan</th>
                            <th>Tanggal & Waktu Kirim</th>
                            <th>Customer</th>
                            <th>Petugas Gudang</th>
                            <th>Catatan Supir / Proyek</th>
                            <th width="14%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengiriman as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td><span class="badge bg-secondary fs-6">{{ $row->penjualan->no_nota ?? '-' }}</span></td>
                            <td>{{ $row->tanggal_kirim ? $row->tanggal_kirim->format('d/m/Y H:i') : '-' }} WIB</td>
                            <td><strong>{{ $row->penjualan->customer->nama_customer ?? '-' }}</strong></td>
                            <td>{{ $row->user->name ?? '-' }}</td>
                            <td>{{ $row->catatan ?? '-' }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.penjualan.pengiriman.show', $row->id) }}" class="btn btn-sm btn-info text-white fw-bold">
                                    <i class="bi bi-eye"></i> Detail Surat Jalan
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <em>Belum ada riwayat pengiriman barang fisik.</em>
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