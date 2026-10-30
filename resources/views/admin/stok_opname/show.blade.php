@extends('layouts.adminlte')

@section('title', 'Detail Stok Opname')
@section('page_title', 'Bukti Hasil Penyesuaian Stok Opname')

@section('content')
<div class="container-fluid">
    <div class="card card-outline card-info shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold m-0"><i class="bi bi-info-circle me-2"></i>Informasi Opname</h5>
            <a href="{{ route('admin.stok-opname.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p class="mb-1 text-muted">Tanggal Opname:</p>
                    <h6 class="fw-bold">{{ \Carbon\Carbon::parse($opname->tanggal)->format('d F Y - H:i') }}</h6>
                </div>
                <div class="col-md-4">
                    <p class="mb-1 text-muted">Petugas Pemeriksa:</p>
                    <h6 class="fw-bold">{{ $opname->user->name ?? '-' }}</h6>
                </div>
                <div class="col-md-4">
                    <p class="mb-1 text-muted">Catatan:</p>
                    <h6 class="fw-bold">{{ $opname->catatan ?? '-' }}</h6>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header">
            <h6 class="card-title fw-bold m-0"><i class="bi bi-box-seam me-2"></i>Rincian Selisih Fisik vs Komputer</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle m-0 text-center">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th class="text-start">Kode & Nama Produk</th>
                            <th>Stok Sistem Awal</th>
                            <th>Stok Fisik Nyata</th>
                            <th>Selisih Penyesuaian</th>
                            <th>Satuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($opname->detailStokOpname as $idx => $d)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td class="text-start">
                                    <span class="badge bg-secondary font-monospace me-1">{{ $d->produk->kode_produk }}</span>
                                    <strong>{{ $d->produk->nama_produk }}</strong>
                                </td>
                                <td class="font-monospace">{{ (float)$d->stok_sistem }}</td>
                                <td class="font-monospace fw-bold">{{ (float)$d->stok_fisik }}</td>
                                <td class="font-monospace fw-bold {{ $d->selisih < 0 ? 'text-danger' : ($d->selisih > 0 ? 'text-success' : 'text-muted') }}">
                                    {{ $d->selisih > 0 ? '+' : '' }}{{ (float)$d->selisih }}
                                </td>
                               <td>{{ $d->produk->satuanDasar->nama_satuan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection