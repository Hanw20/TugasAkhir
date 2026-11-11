@extends('layouts.adminlte')

@section('title', 'Riwayat Konversi Produk')
@section('page_title', 'Konversi Produk (Repacking Satuan)')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-header py-2 text-white d-flex justify-content-between align-items-center" style="background-color: #264653; border-top-left-radius: 10px; border-top-right-radius: 10px;">
            <h6 class="card-title fw-bold m-0 text-white"><i class="bi bi-arrow-repeat me-2"></i>Daftar Riwayat Konversi Kemasan</h6>
            <a href="{{ route('admin.konversi-produk.create') }}" class="btn btn-sm text-white shadow-sm" style="background-color: #2A9D8F; border-radius: 6px;">
                <i class="bi bi-plus-circle me-1"></i> Konversi Produk Baru
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle m-0 text-center">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Tanggal</th>
                            <th class="text-start">Produk Asal (Dipakai)</th>
                            <th></th>
                            <th class="text-start">Produk Tujuan (Hasil)</th>
                            <th>Petugas</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $idx => $row)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td class="fw-bold">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                                <td class="text-start">
                                    <span class="badge bg-danger me-1">- {{ (float)$row->qty_asal_dipakai }} {{ $row->produkAsal->satuan->nama_satuan ?? '' }}</span>
                                    <strong>{{ $row->produkAsal->nama_produk ?? '-' }}</strong>
                                </td>
                                <td><i class="bi bi-arrow-right-circle fs-5 text-primary"></i></td>
                                <td class="text-start">
                                    <span class="badge bg-success me-1">+ {{ (float)$row->qty_hasil_aktual }} {{ $row->produkTujuan->satuan->nama_satuan ?? '' }}</span>
                                    <strong>{{ $row->produkTujuan->nama_produk ?? '-' }}</strong>
                                </td>
                                <td>{{ $row->user->name ?? '-' }}</td>
                                <td><small class="text-muted">{{ $row->catatan ?? '-' }}</small></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    Belum ada riwayat konversi kemasan produk.
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