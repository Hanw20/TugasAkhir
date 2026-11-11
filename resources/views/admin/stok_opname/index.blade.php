@extends('layouts.adminlte')

@section('title', 'Riwayat Stok Opname')
@section('page_title', 'Stok Opname (Penyesuaian Fisik Gudang)')

@section('content')
 <div class="container-fluid">
 <div class="card" >
 <div class="card-header py-2 text-white d-flex justify-content-between align-items-center" style="background-color: #264653; border-top-left-radius: 10px; border-top-right-radius: 10px;">
 <h6 class="card-title fw-bold m-0 text-white"><i class="bi bi-clipboard-check me-2"></i>Daftar Riwayat Stok Opname</h6>
 <a href="{{ route('admin.stok-opname.create') }}" class="btn btn-sm text-white" style="background-color: #2A9D8F;">
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
 <th>Status</th>
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
 <td><span
 class="badge" style="background-color: #E0F2FE; color: #0369A1;">{{ $row->detailStokOpname ? $row->detailStokOpname->count() : 0 }}
 Produk</span></td>
 <td>
 @if(($row->status ?? 'menunggu_persetujuan') === 'disesuaikan')
 <span class="badge" style="background-color: #D1FAE5; color: #065F46;"><i class="bi bi-check-circle me-1"></i>
 Disesuaikan</span>
 @else
 <span class="badge" style="background-color: #FEF3C7; color: #B45309;"><i class="bi bi-hourglass-split me-1"></i>
 Menunggu Persetujuan</span>
 @endif
 </td>
 <td><small class="text-muted">{{ $row->catatan ?? '-' }}</small></td>
 <td class="text-center">
 <a href="{{ route('admin.stok-opname.show', $row->id) }}"
 class="btn btn-sm" style="background-color: #E0F2FE; color: #0369A1; font-weight: 500; font-size: 0.8rem; padding: 4px 10px;">
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