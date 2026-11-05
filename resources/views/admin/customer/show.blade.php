@extends('layouts.adminlte')

@section('title', 'Detail Customer')
@section('page_title', 'Detail Customer: ' . $customer->nama_customer)

@section('content')
    <div class="row">
        <!-- 1. CARD PROFIL CUSTOMER (FULL WIDTH DI ATAS) -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-person-badge me-2"></i> Profil Pelanggan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0 align-middle">
                            <tbody>
                                <tr>
                                    <th width="20%" class="ps-3">Nama Pelanggan</th>
                                    <td class="fw-bold fs-6">{{ $customer->nama_customer }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-3">Nomor Telepon</th>
                                    <td>{{ $customer->no_telepon ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-3">Alamat</th>
                                    <td>{{ $customer->alamat ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-3">Total Transaksi</th>
                                    <td>
                                        <span class="badge bg-primary fs-6">
                                            {{ $customer->penjualan->count() }} Kali Belanja
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light py-2">
                    <a href="{{ route('admin.customer.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Customer
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. CARD RIWAYAT TRANSAKSI BELANJA (FULL WIDTH DI BAWAH) -->
        <div class="col-12">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title fw-bold m-0"><i class="bi bi-receipt me-2"></i> Riwayat Transaksi Belanja
                        Pelanggan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle m-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th>No Nota</th>
                                    <th>Tanggal Transaksi</th>
                                    <th class="text-end">Total Belanja</th>
                                    <th class="text-center">Status</th>
                                    <th width="10%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customer->penjualan as $index => $pj)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <!-- FIX no_nota: sebelumnya salah no_faktur -->
                                        <td><span class="badge bg-secondary font-monospace fs-6">{{ $pj->no_nota }}</span></td>
                                        <td>{{ $pj->tanggal ? $pj->tanggal->format('d/m/Y') : '-' }}</td>
                                        <!-- FIX total: sebelumnya salah total_akhir -->
                                        <td class="text-end fw-bold text-success fs-6">Rp
                                            {{ number_format($pj->total, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <!-- Status Pengiriman Barang -->
                                            @if($pj->status_pengiriman === 'seluruh')
                                                <span class="badge bg-info text-dark">Terkirim / Ambil</span>
                                            @elseif($pj->status_pengiriman === 'dikirim_sebagian')
                                                <span class="badge bg-warning text-dark">Kirim Sebagian</span>
                                            @else
                                                <span class="badge bg-secondary">Belum Dikirim</span>
                                            @endif

                                            <br>

                                            <!-- Status Pembayaran -->
                                            @if($pj->status_pembayaran === 'lunas')
                                                <span class="badge bg-success mt-1">Lunas</span>
                                            @else
                                                <span class="badge bg-danger mt-1">Tempo (Piutang)</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.penjualan.show', $pj->id) }}"
                                                class="btn btn-sm btn-info text-white">
                                                <i class="bi bi-eye"></i> Nota
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                            <em>Belum ada riwayat transaksi penjualan yang tercatat untuk pelanggan ini.</em>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection