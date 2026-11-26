@extends('layouts.adminlte')

@section('title', 'Nota Penjualan')
@section('page_title', 'Nota Penjualan: #' . $penjualan->no_nota)

@section('content')
    <div class="row">
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="card">
                <div class="card-header text-white d-flex justify-content-between align-items-center"
                    style="background-color: #264653;">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-receipt me-2"></i> Nota Penjualan
                        #{{ $penjualan->no_nota }}
                    </h5>
                    @if($penjualan->status === 'batal')
                        <span class="badge bg-danger text-white"
                            style="font-weight: 600; padding: 6px 12px;"><i class="bi bi-x-circle me-1"></i> Dibatalkan</span>
                    @else
                        <span class="badge"
                            style="background-color: #D1FAE5; color: #065F46; font-weight: 600; padding: 6px 12px;">{{ ucfirst($penjualan->status_pembayaran) }}</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-dark border-bottom pb-2">Informasi Transaksi</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="35%">Nomor Nota</th>
                                    <td>: <strong>{{ $penjualan->no_nota }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Tanggal</th>
                                    <td>: {{ $penjualan->tanggal ? $penjualan->tanggal->format('d F Y - H:i') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Petugas</th>
                                    <td>: {{ $penjualan->user->name ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-dark border-bottom pb-2">Informasi Pelanggan</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="35%">Nama Customer</th>
                                    <td>: <strong>{{ $penjualan->customer->nama_customer ?? 'Umum' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>No Telepon</th>
                                    <td>: {{ $penjualan->customer->no_telepon ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td>: {{ $penjualan->customer->alamat ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-2">Item Barang yang Dibeli</h6>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th>Nama Produk</th>
                                    <th class="text-end">Harga Satuan</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($penjualan->detailPenjualan as $index => $item)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $item->produk->nama_produk ?? '-' }}</strong><br>
                                            <small class="text-muted">{{ $item->produk->kode_produk ?? '' }}</small>
                                        </td>
                                        <td class="text-end">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="text-center fw-bold">{{ (float) $item->qty }}
                                            {{ $item->produk->satuanDasar->nama_satuan ?? '' }}
                                        </td>
                                        <td class="text-end fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                @if(($penjualan->diskon > 0) || ($penjualan->ongkos_kirim > 0))
                                    <tr>
                                        <th colspan="4" class="text-end">Subtotal:</th>
                                        <th class="text-end">Rp {{ number_format($penjualan->detailPenjualan->sum('subtotal'), 0, ',', '.') }}</th>
                                    </tr>
                                @endif
                                @if($penjualan->diskon > 0)
                                    <tr>
                                        <th colspan="4" class="text-end text-danger">Potongan / Diskon:</th>
                                        <th class="text-end text-danger">- Rp {{ number_format($penjualan->diskon, 0, ',', '.') }}</th>
                                    </tr>
                                @endif
                                @if($penjualan->ongkos_kirim > 0)
                                    <tr>
                                        <th colspan="4" class="text-end text-primary">Ongkos Kirim:</th>
                                        <th class="text-end text-primary">+ Rp {{ number_format($penjualan->ongkos_kirim, 0, ',', '.') }}</th>
                                    </tr>
                                @endif
                                <tr>
                                    <th colspan="4" class="text-end fs-6 fw-bold">TOTAL PEMBAYARAN:</th>
                                    <th class="text-end fs-6 fw-bold" style="color: #264653;">Rp
                                        {{ number_format($penjualan->total, 0, ',', '.') }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between bg-white py-3 border-0">
                    <a href="{{ route('admin.penjualan.index') }}" class="btn btn-sm"
                        style="background-color: #E2E8F0; color: #334155; font-weight: 500; padding: 7px 16px;">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat
                    </a>
                    <div class="d-flex gap-2">
                        @if($penjualan->status !== 'batal' && ($penjualan->status_pengiriman === 'belum_dikirim' || ($penjualan->pengirimanPenjualan->count() === 1 && $penjualan->pengirimanPenjualan->first()->catatan === 'Barang langsung diserahkan di toko (Bawa Langsung)')))
                            <button type="button" class="btn btn-sm btn-outline-danger fw-bold" data-bs-toggle="modal"
                                data-bs-target="#modalBatalPenjualan" style="padding: 7px 16px;">
                                <i class="bi bi-x-circle me-1"></i> Batalkan Nota
                            </button>
                        @endif

                        @if($penjualan->status !== 'batal' && $penjualan->status_pengiriman !== 'seluruh')
                            <a href="{{ route('admin.penjualan.kirim.create', $penjualan->id) }}"
                                class="btn btn-sm text-white fw-bold" style="background-color: #2A9D8F; padding: 7px 18px;">
                                <i class="bi bi-truck me-1"></i> Kirim Barang
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($penjualan->status !== 'batal' && ($penjualan->status_pengiriman === 'belum_dikirim' || ($penjualan->pengirimanPenjualan->count() === 1 && $penjualan->pengirimanPenjualan->first()->catatan === 'Barang langsung diserahkan di toko (Bawa Langsung)')))
        <div class="modal fade" id="modalBatalPenjualan" tabindex="-1" aria-labelledby="modalBatalPenjualanLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header text-white" style="background-color: #E76F51;">
                        <h5 class="modal-title fw-bold" id="modalBatalPenjualanLabel"><i class="bi bi-exclamation-triangle-fill me-2"></i> Konfirmasi Pembatalan Penjualan</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.penjualan.destroy', $penjualan->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-body p-4 text-center">
                            <i class="bi bi-x-circle text-danger" style="font-size: 3rem;"></i>
                            <h5 class="fw-bold mt-3 mb-2">Batalkan Nota Penjualan #{{ $penjualan->no_nota }}?</h5>
                            <p class="text-muted mb-0">Apakah Anda yakin ingin membatalkan transaksi penjualan ini? Stok barang akan dikembalikan ke gudang dan status nota akan diubah menjadi <strong>Batal</strong>.</p>
                        </div>
                        <div class="modal-footer bg-light border-0 d-flex justify-content-between">
                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-sm btn-danger fw-bold px-3">Ya, Batalkan Nota</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection