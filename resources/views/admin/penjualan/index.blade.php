@extends('layouts.adminlte')

@section('title', 'Laporan Penjualan')
@section('page_title', 'Data & Laporan Riwayat Penjualan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="mb-3">
            <a href="{{ route('admin.penjualan.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> + Input Transaksi Penjualan
            </a>
        </div>

        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0"><i class="bi bi-receipt me-2"></i> Rekapitulasi Nota Penjualan</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th>No Nota</th>
                            <th>Tanggal</th>
                            <th>Customer / Pelanggan</th>
                            <th>Kasir / Petugas</th>
                            <th class="text-end">Total Belanja</th>
                            <th class="text-center">Status</th>
                            <th width="12%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penjualan as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td><span class="badge bg-secondary fs-6">{{ $row->no_nota }}</span></td>
                            <td>{{ $row->tanggal ? $row->tanggal->format('d/m/Y') : '-' }}</td>
                            <td><strong>{{ $row->customer->nama_customer ?? 'Umum' }}</strong></td>
                            <td>{{ $row->user->name ?? '-' }}</td>
                            <td class="text-end fw-bold text-success">Rp {{ number_format($row->total, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <span class="badge bg-success">Lunas</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.penjualan.show', $row->id) }}" class="btn btn-sm btn-info text-white">
                                    <i class="bi bi-eye"></i> Nota
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <em>Belum ada transaksi penjualan yang tercatat.</em>
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