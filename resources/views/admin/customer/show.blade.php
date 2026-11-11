@extends('layouts.adminlte')

@section('title', 'Detail Customer')
@section('page_title', 'Detail Customer: ' . $customer->nama_customer)

@section('content')
    <div class="row">
        <!-- 1. CARD PROFIL CUSTOMER (FULL WIDTH DI ATAS) -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0" style="border-radius: 10px; overflow: hidden;">
                <div class="card-header text-white" style="background-color: #264653;">
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
                                        <span class="badge" style="background-color: #E0F2FE; color: #0369A1; font-size: 0.85rem;">
                                            {{ $customer->penjualan->count() }} Kali Belanja
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light py-2">
                    <a href="{{ route('admin.customer.index') }}" class="btn btn-sm" style="background-color: #E2E8F0; color: #334155; font-weight: 500;">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Customer
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. CARD RIWAYAT TRANSAKSI BELANJA (FULL WIDTH DI BAWAH) -->
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 10px; overflow: hidden;">
                <div class="card-header text-white" style="background-color: #264653;">
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
                                        <td><span class="badge" style="background-color: #F1F5F9; color: #475569; font-family: monospace;">{{ $pj->no_nota }}</span></td>
                                        <td>{{ $pj->tanggal ? $pj->tanggal->format('d/m/Y') : '-' }}</td>
                                        <!-- FIX total: sebelumnya salah total_akhir -->
                                        <td class="text-end fw-bold text-dark fs-6">Rp
                                            {{ number_format($pj->total, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <!-- Status Pengiriman Barang -->
                                            @if($pj->status_pengiriman === 'seluruh')
                                                <span class="badge" style="background-color: #E0F2FE; color: #0369A1;">Terkirim / Ambil</span>
                                            @elseif($pj->status_pengiriman === 'dikirim_sebagian')
                                                <span class="badge" style="background-color: #FEF3C7; color: #B45309;">Kirim Sebagian</span>
                                            @else
                                                <span class="badge" style="background-color: #F1F5F9; color: #475569;">Belum Dikirim</span>
                                            @endif

                                            <br>

                                            <!-- Status Pembayaran -->
                                            @if($pj->status_pembayaran === 'lunas')
                                                <span class="badge mt-1" style="background-color: #D1FAE5; color: #065F46;">Lunas</span>
                                            @else
                                                <span class="badge mt-1" style="background-color: #FEE2E2; color: #B91C1C;">Tempo (Piutang)</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.penjualan.show', $pj->id) }}"
                                                class="btn btn-sm text-decoration-none" style="background-color: #E0F2FE; color: #0369A1; font-weight: 500;">
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