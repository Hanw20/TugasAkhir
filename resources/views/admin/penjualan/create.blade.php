@extends('layouts.adminlte')

@section('title', 'Input Penjualan')
@section('page_title', 'Catat Transaksi Penjualan Baru')

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.penjualan.store') }}" method="POST">
            @csrf

            <!-- Header Nota -->
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0"><i class="bi bi-file-earmark-text me-2"></i> Data Nota Penjualan</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Nomor Nota</label>
                            <input type="text" name="no_nota" class="form-control" value="{{ $noNotaOtomatis }}" readonly required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tanggal Transaksi</label>
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Pilih Customer</label>
                            <select name="customer_id" class="form-select" required>
                                <option value="">-- Pilih Customer --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->nama_customer }} ({{ $c->no_telepon ?? '-' }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Barang Belanja -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-cart me-2"></i> Daftar Barang yang Dijual</h5>
                    <button type="button" class="btn btn-sm btn-light fw-bold" onclick="tambahBaris()">
                        <i class="bi bi-plus-circle"></i> + Tambah Item Barang
                    </button>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="35%">Nama Produk</th>
                                <th width="15%">Satuan</th>
                                <th width="15%">Harga Jual (Rp)</th>
                                <th width="12%">Qty</th>
                                <th width="18%">Subtotal (Rp)</th>
                                <th width="5%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="baris-penjualan-container">
                            <tr class="item-row">
                                <td>
                                    <select name="produk_id[]" class="form-select select-produk" required onchange="pilihProduk(this)">
                                        <option value="">-- Pilih Produk --</option>
                                        @foreach($produks as $p)
                                            <option value="{{ $p->id }}" data-harga="{{ (int)$p->harga_jual }}" data-satuan="{{ $p->satuanDasar->nama_satuan ?? '-' }}" data-stok="{{ (float)$p->stok }}">
                                                {{ $p->kode_produk }} - {{ $p->nama_produk }} (Stok: {{ (float)$p->stok }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control label-satuan" readonly placeholder="-">
                                </td>
                                <td>
                                    <input type="number" name="harga_satuan[]" class="form-control input-harga" required min="0" oninput="hitungSubtotal(this)">
                                </td>
                                <td>
                                    <input type="number" name="qty[]" class="form-control input-qty" required min="1" value="1" oninput="hitungSubtotal(this)">
                                </td>
                                <td>
                                    <input type="text" class="form-control text-end input-subtotal" readonly value="0">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusBaris(this)" disabled>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4" class="text-end fs-5">TOTAL AKHIR PENJUALAN:</th>
                                <th class="text-end fs-5 text-success fw-bold" id="label-total-akhir">Rp 0</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('admin.penjualan.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-success px-4 fw-bold">
                        <i class="bi bi-check2-circle"></i> Simpan Transaksi Penjualan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const optionsProduk = `
        <option value="">-- Pilih Produk --</option>
        @foreach($produks as $p)
            <option value="{{ $p->id }}" data-harga="{{ (int)$p->harga_jual }}" data-satuan="{{ $p->satuanDasar->nama_satuan ?? '-' }}" data-stok="{{ (float)$p->stok }}">
                {{ $p->kode_produk }} - {{ $p->nama_produk }} (Stok: {{ (float)$p->stok }})
            </option>
        @endforeach
    `;

    function tambahBaris() {
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td>
                <select name="produk_id[]" class="form-select select-produk" required onchange="pilihProduk(this)">
                    ${optionsProduk}
                </select>
            </td>
            <td><input type="text" class="form-control label-satuan" readonly placeholder="-"></td>
            <td><input type="number" name="harga_satuan[]" class="form-control input-harga" required min="0" oninput="hitungSubtotal(this)"></td>
            <td><input type="number" name="qty[]" class="form-control input-qty" required min="1" value="1" oninput="hitungSubtotal(this)"></td>
            <td><input type="text" class="form-control text-end input-subtotal" readonly value="0"></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger" onclick="hapusBaris(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        document.getElementById('baris-penjualan-container').appendChild(tr);
        updateTombolHapus();
    }

    function hapusBaris(btn) {
        btn.closest('tr').remove();
        hitungTotalSemua();
        updateTombolHapus();
    }

    function updateTombolHapus() {
        const rows = document.querySelectorAll('.item-row');
        rows.forEach(row => {
            const btnHapus = row.querySelector('.btn-danger');
            if (btnHapus) btnHapus.disabled = (rows.length === 1);
        });
    }

    function pilihProduk(selectEl) {
        const row = selectEl.closest('tr');
        const selected = selectEl.options[selectEl.selectedIndex];
        const harga = selected.getAttribute('data-harga') || 0;
        const satuan = selected.getAttribute('data-satuan') || '-';
        const stok = parseFloat(selected.getAttribute('data-stok')) || 0;

        row.querySelector('.input-harga').value = harga;
        row.querySelector('.label-satuan').value = satuan;
        row.querySelector('.input-qty').max = stok;
        hitungSubtotal(selectEl);
    }

    function hitungSubtotal(inputEl) {
        const row = inputEl.closest('tr');
        const harga = parseFloat(row.querySelector('.input-harga').value) || 0;
        const qty = parseFloat(row.querySelector('.input-qty').value) || 0;
        const subtotal = harga * qty;

        row.querySelector('.input-subtotal').value = new Intl.NumberFormat('id-ID').format(subtotal);
        hitungTotalSemua();
    }

    function hitungTotalSemua() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const harga = parseFloat(row.querySelector('.input-harga').value) || 0;
            const qty = parseFloat(row.querySelector('.input-qty').value) || 0;
            total += (harga * qty);
        });
        document.getElementById('label-total-akhir').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
    }
</script>
@endpush