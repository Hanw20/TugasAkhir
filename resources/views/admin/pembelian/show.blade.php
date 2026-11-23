@extends('layouts.adminlte')

@section('title', 'Detail Faktur Pembelian')
@section('page_title', 'Faktur Pembelian: ' . $pembelian->no_faktur)

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
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-receipt me-2"></i> Faktur Pembelian
                        #{{ $pembelian->no_faktur }}</h5>
                    @if($pembelian->status === 'batal')
                        <span class="badge bg-danger text-white"
                            style="font-weight: 600; padding: 6px 12px;"><i class="bi bi-x-circle me-1"></i> Dibatalkan</span>
                    @else
                        <span class="badge"
                            style="background-color: #FEF3C7; color: #B45309; font-weight: 600; padding: 6px 12px;">{{ ucfirst($pembelian->status) }}</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-dark">Informasi Faktur</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="35%">No Faktur</th>
                                    <td>: <strong>{{ $pembelian->no_faktur }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Tanggal</th>
                                    <td>: {{ $pembelian->tanggal->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Petugas Input</th>
                                    <td>: {{ $pembelian->user->name ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-dark">Informasi Supplier</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="35%">Supplier</th>
                                    <td>: <strong>{{ $pembelian->supplier->nama_supplier ?? '-' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>No Telepon</th>
                                    <td>: {{ $pembelian->supplier->nomor_telepon ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td>: {{ $pembelian->supplier->alamat ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Kode Produk</th>
                                    <th>Nama Produk</th>
                                    <th>Harga Beli Satuan</th>
                                    <th class="text-center">Qty Masuk</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pembelian->detailPembelian as $index => $detail)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><span class="badge"
                                                style="background-color: #F1F5F9; color: #475569; font-family: monospace;">{{ $detail->produk->kode_produk ?? '-' }}</span>
                                        </td>
                                        <td><strong>{{ $detail->produk->nama_produk ?? '-' }}</strong></td>
                                        <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="text-center fw-bold">
                                            {{ (int) $detail->qty }} {{ $detail->produk->satuanDasar->nama_satuan ?? '' }}
                                        </td>
                                        <td class="text-end fw-bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="5" class="text-end fs-6 fw-bold">TOTAL AKHIR:</th>
                                    <th class="text-end fs-6 fw-bold" style="color: #264653;">
                                        Rp {{ number_format($pembelian->total, 0, ',', '.') }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between bg-white py-3 border-0">
                    <a href="{{ route('admin.pembelian.index') }}" class="btn btn-sm"
                        style="background-color: #E2E8F0; color: #334155; font-weight: 500; padding: 7px 16px;">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat
                    </a>
                    <div class="d-flex gap-2">
                        @if($pembelian->status !== 'batal' && $pembelian->status_penerimaan === 'belum_diterima')
                            <button type="button" class="btn btn-sm btn-outline-danger fw-bold" data-bs-toggle="modal"
                                data-bs-target="#modalBatalPembelian" style="padding: 7px 16px;">
                                <i class="bi bi-x-circle me-1"></i> Batalkan Faktur
                            </button>
                        @endif

                        @if($pembelian->status !== 'batal' && $pembelian->status_penerimaan !== 'terima_penuh')
                            <a href="{{ route('admin.pembelian.terima.create', $pembelian->id) }}"
                                class="btn btn-sm text-white fw-bold" style="background-color: #2A9D8F; padding: 7px 18px;">
                                <i class="bi bi-box-arrow-in-down me-1"></i> Terima Barang Datang
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($pembelian->status !== 'batal' && $pembelian->status_penerimaan === 'belum_diterima')
        <div class="modal fade" id="modalBatalPembelian" tabindex="-1" aria-labelledby="modalBatalPembelianLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header text-white" style="background-color: #E76F51;">
                        <h5 class="modal-title fw-bold" id="modalBatalPembelianLabel"><i class="bi bi-exclamation-triangle-fill me-2"></i> Konfirmasi Pembatalan Faktur</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.pembelian.destroy', $pembelian->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-body p-4 text-center">
                            <i class="bi bi-x-circle text-danger" style="font-size: 3rem;"></i>
                            <h5 class="fw-bold mt-3 mb-2">Batalkan Faktur PO #{{ $pembelian->no_faktur }}?</h5>
                            <p class="text-muted mb-0">Apakah Anda yakin ingin membatalkan faktur pembelian ini? Kuota pesanan ke supplier pada stok produk akan dikembalikan dan status faktur akan diubah menjadi <strong>Batal</strong>.</p>
                        </div>
                        <div class="modal-footer bg-light border-0 d-flex justify-content-between">
                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-sm btn-danger fw-bold px-3">Ya, Batalkan Faktur</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection