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

            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('admin.pembelian.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Transaksi Pembelian Baru
                </a>

                <!-- Form Filter Periode Bulan -->
                <form method="GET" action="{{ route('admin.pembelian.index') }}" class="d-flex align-items-center gap-2">
                    <label class="mb-0 fw-bold me-1">Filter:</label>
                    <select name="bulan" class="form-select form-select-sm" style="width: 140px;">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ sprintf('%02d', $m) }}" {{ $bulan == sprintf('%02d', $m) ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                            </option>
                        @endfor
                    </select>
                    <select name="tahun" class="form-select form-select-sm" style="width: 100px;">
                        @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <button type="submit" class="btn btn-sm btn-secondary">
                        <i class="bi bi-filter"></i> Terapkan
                    </button>
                </form>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-receipt me-2"></i> Riwayat Faktur Pembelian</h5>
                    <span class="badge bg-light text-dark">Periode:
                        {{ DateTime::createFromFormat('!m', (int) $bulan)->format('F') }} {{ $tahun }}</span>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>No Faktur</th>
                                <th>Tanggal</th>
                                <th>Supplier</th>
                                <th>Petugas / User</th>
                                <th class="text-end">Total Pembelian</th>
                                <th class="text-center">Status</th>
                                <th width="12%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pembelian as $index => $row)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td><span class="badge bg-secondary">{{ $row->no_faktur }}</span></td>
                                    <td>{{ $row->tanggal ? $row->tanggal->format('d/m/Y') : '-' }}</td>
                                    <td><strong>{{ $row->supplier->nama_supplier ?? '-' }}</strong></td>
                                    <td>{{ $row->user->name ?? '-' }}</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($row->total, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <!-- Status Penerimaan Barang Fisik -->
                                        @if($row->status_penerimaan === 'terima_penuh')
                                            <span class="badge bg-success"><i class="bi bi-box-seam me-1"></i> Diterima Penuh</span>
                                        @elseif($row->status_penerimaan === 'sebagian')
                                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>
                                                Diterima Sebagian</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="bi bi-truck me-1"></i> Belum Diterima</span>
                                        @endif

                                        <br>

                                        <!-- Status Pembayaran Hutang -->
                                        @if($row->status_pembayaran === 'lunas')
                                            <span
                                                class="badge bg-success-subtle text-success border border-success mt-1">Lunas</span>
                                        @elseif($row->status_pembayaran === 'sebagian')
                                            <span class="badge bg-warning-subtle text-dark border border-warning mt-1">Bayar
                                                Sebagian</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger mt-1">Hutang
                                                (Tempo)</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.pembelian.show', $row->id) }}"
                                            class="btn btn-sm btn-info text-white">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-2 d-block mb-1"></i> Tidak ada transaksi pembelian pada periode
                                        ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <!-- Baris Total Akumulasi -->
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="5" class="text-end text-uppercase">Total Pembelian Periode Ini:</td>
                                <td class="text-end text-primary fs-6">Rp {{ number_format($totalAkumulasi, 0, ',', '.') }}
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