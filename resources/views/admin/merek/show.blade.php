@extends('layouts.adminlte')

@section('title', 'Detail Merek')
@section('page_title', 'Merek: ' . $merek->nama_merek)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h3 class="card-title">Daftar Produk dengan Merek: <strong>{{ $merek->nama_merek }}</strong></h3>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Kode Produk</th>
                            <th>Nama Produk</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($merek->produk as $index => $prod)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $prod->kode_produk }}</td>
                            <td>{{ $prod->nama_produk }}</td>
                            <td>Rp {{ number_format($prod->harga_jual, 0, ',', '.') }}</td>
                            <td>{{ (int) $prod->stok }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-danger">Belum ada produk untuk merek ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.merek.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Merek
                </a>
            </div>
        </div>
    </div>
</div>
@endsection