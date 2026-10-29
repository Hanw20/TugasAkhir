@extends('layouts.adminlte')

@section('title', 'Detail Customer')
@section('page_title', 'Detail Customer: ' . $customer->nama_customer)

@section('content')
<div class="row">
    <!-- Kolom Kiri: Profil Customer -->
    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h3 class="card-title"><i class="bi bi-person-badge me-2"></i> Profil Pelanggan</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <tr>
                        <th width="40%">Nama Pelanggan</th>
                        <td><strong>{{ $customer->nama_customer }}</strong></td>
                    </tr>
                    <tr>
                        <th>Nomor Telepon</th>
                        <td>{{ $customer->no_telepon ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $customer->alamat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Total Transaksi</th>
                        <td>
                            <span class="badge bg-primary">
                                {{ $customer->penjualan->count() }} Kali Belanja
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.customer.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Customer
                </a>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Riwayat Belanja / Transaksi -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h3 class="card-title"><i class="bi bi-receipt me-2"></i> Riwayat Transaksi Belanja</h3>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>No Faktur</th>
                            <th>Tanggal</th>
                            <th>Total Belanja</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customer->penjualan as $index => $pj)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $pj->no_faktur }}</td>
                            <td>{{ $pj->tanggal->format('d/m/Y') }}</td>
                            <td>Rp {{ number_format($pj->total_akhir, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                <em>Belum ada transaksi penjualan yang tercatat untuk pelanggan ini.</em>
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