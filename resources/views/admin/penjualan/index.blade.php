@extends('layouts.adminlte')

@section('title', 'Laporan Penjualan')
@section('page_title', 'Data & Laporan Riwayat Penjualan')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('admin.penjualan.create') }}" class="btn text-white shadow-sm" style="background-color: #2A9D8F; border-radius: 8px; font-weight: 500; padding: 7px 16px;">
                    <i class="bi bi-plus-circle me-1"></i> Transaksi Penjualan Baru
                </a>

                <!-- Form Filter Periode Bulan -->
                <form method="GET" action="{{ route('admin.penjualan.index') }}" class="d-flex align-items-center gap-2">
                    <label class="mb-0 fw-semibold text-muted me-1">Filter:</label>
                    <select name="bulan" class="form-select form-select-sm" style="width: 140px; border-radius: 6px;">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ sprintf('%02d', $m) }}" {{ $bulan == sprintf('%02d', $m) ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                            </option>
                        @endfor
                    </select>
                    <select name="tahun" class="form-select form-select-sm" style="width: 100px; border-radius: 6px;">
                        @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <button type="submit" class="btn btn-sm btn-secondary" style="border-radius: 6px;">
                        <i class="bi bi-filter"></i> Terapkan
                    </button>
                </form>
            </div>

            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header py-2 text-white d-flex justify-content-between align-items-center" style="background-color: #264653; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                    <h6 class="card-title fw-bold m-0 text-white"><i class="bi bi-receipt me-2"></i> Rekapitulasi Nota Penjualan</h6>
                    <span class="badge bg-light text-dark">Periode:
                        {{ DateTime::createFromFormat('!m', (int) $bulan)->format('F') }} {{ $tahun }}</span>
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
                                    <td class="text-end fw-bold text-success">Rp {{ number_format($row->total, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        <!-- Status Pengiriman Barang -->
                                        @if($row->status_pengiriman === 'seluruh')
                                            <span class="badge mt-1" style="background-color: #E0F2FE; color: #0369A1;">Terkirim / Ambil</span>
                                        @elseif($row->status_pengiriman === 'dikirim_sebagian')
                                            <span class="badge mt-1" style="background-color: #FEF3C7; color: #B45309;">Kirim Sebagian</span>
                                        @else
                                            <span class="badge mt-1" style="background-color: #F1F5F9; color: #475569;">Belum Dikirim</span>
                                        @endif
                                        <br>
                                        <!-- Status Pembayaran -->
                                        @if($row->status_pembayaran === 'lunas')
                                            <span class="badge mt-1" style="background-color: #D1FAE5; color: #065F46;">Lunas</span>
                                        @else
                                            <span class="badge mt-1" style="background-color: #FEE2E2; color: #B91C1C;">Tempo (Piutang)</span>
                                        @endif




                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.penjualan.show', $row->id) }}"
                                            class="btn btn-sm" style="background-color: #E0F2FE; color: #0369A1; border-radius: 6px; font-weight: 500; font-size: 0.8rem; padding: 4px 10px;">
                                            <i class="bi bi-eye"></i> Nota
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-2 d-block mb-1"></i> Belum ada transaksi penjualan pada periode
                                        ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <!-- Baris Total Akumulasi -->
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="5" class="text-end text-uppercase">Total Penjualan Periode Ini:</td>
                                <td class="text-end text-success fs-6">Rp {{ number_format($totalAkumulasi, 0, ',', '.') }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection