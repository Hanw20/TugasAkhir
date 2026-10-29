@extends('layouts.adminlte')

@section('title', 'Riwayat Pembelian')
@section('page_title', 'Transaksi Pembelian (Barang Masuk)')

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="mb-3">
            <a href="{{ route('admin.pembelian.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Transaksi Pembelian Baru
            </a>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title mb-0"><i class="bi bi-receipt me-2"></i> Riwayat Faktur Pembelian</h3>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>No Faktur</th>
                            <th>Tanggal</th>
                            <th>Supplier</th>
                            <th>Petugas / User</th>
                            <th>Total Pembelian</th>
                            <th>Status</th>
                            <th width="12%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pembelian as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><span class="badge bg-secondary">{{ $row->no_faktur }}</span></td>
                            <td>{{ $row->tanggal ? $row->tanggal->format('d/m/Y') : '-' }}</td>
                            <td><strong>{{ $row->supplier->nama_supplier ?? '-' }}</strong></td>
                            <td>{{ $row->user->name ?? '-' }}</td>
                            <td class="text-end fw-bold">Rp {{ number_format($row->total, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-success">{{ ucfirst($row->status ?? 'selesai') }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.pembelian.show', $row->id) }}" class="btn btn-sm btn-info text-white">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <em>Belum ada riwayat transaksi pembelian. Silakan klik tombol "Transaksi Pembelian Baru" di atas.</em>
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