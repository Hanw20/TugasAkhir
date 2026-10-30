@extends('layouts.adminlte')

@section('title', 'Riwayat Stok Opname')
@section('page_title', 'Stok Opname (Penyesuaian Fisik Gudang)')

@section('content')
<div class="container-fluid">
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold m-0"><i class="bi bi-clipboard-check me-2"></i>Daftar Riwayat Stok Opname</h5>
            <a href="{{ route('admin.stok-opname.create') }}" class="btn btn-primary btn-sm fw-bold">
                <i class="bi bi-plus-circle me-1"></i> Input Stok Opname Baru
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle m-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;" class="text-center">No</th>
                            <th>Tanggal Opname</th>
                            <th>Petugas / User</th>
                            <th>Jumlah Item Diperiksa</th>
                            <th>Catatan</th>
                            <th class="text-center" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $idx => $row)
                            <tr>
                                <td class="text-center">{{ $idx + 1 }}</td>
                                <td class="fw-bold">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y H:i') }}</td>
                                <td>{{ $row->user->name ?? '-' }}</td>
                                <td><span class="badge bg-info text-dark">{{ $row->detailStokOpname->count() }} Produk</span></td>
                                <td><small class="text-muted">{{ $row->catatan ?? '-' }}</small></td>
                                <td class="text-center">
                                    <a href="{{ route('admin.stok-opname.show', $row->id) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    Belum ada catatan stok opname. Klik tombol di atas untuk memulai penyesuaian stok fisik.
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