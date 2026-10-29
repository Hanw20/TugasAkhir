@extends('layouts.adminlte')

@section('title', 'Tambah Pembelian')
@section('page_title', 'Input Transaksi Pembelian Baru')

@section('content')
    <div class="row">
        <div class="col-12">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('admin.pembelian.store') }}" method="POST" id="form-pembelian">
                @csrf

                <!-- Card Informasi Faktur -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0"><i class="bi bi-file-earmark-text me-2"></i> Data Faktur Pembelian</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Nomor Faktur</label>
                                <input type="text" name="no_faktur" class="form-control" value="{{ $noFakturOtomatis }}"
                                    required readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tanggal Pembelian</label>
                                <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Pilih Supplier</label>
                                <select name="supplier_id" class="form-select" required>
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $sup)
                                        <option value="{{ $sup->id }}">{{ $sup->kode_supplier }} - {{ $sup->nama_supplier }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Daftar Item Barang -->
                <div class="card mb-3">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="bi bi-cart-plus me-2"></i> Daftar Barang Dipesan (Purchase
                            Order)</h5>
                        <button type="button" class="btn btn-sm btn-light" onclick="tambahBarisItem()">
                            <i class="bi bi-plus-circle"></i> + Tambah Item Barang
                        </button>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-bordered mb-0 align-middle" id="tabel-item">
                            <thead class="table-light">
                                <tr>
                                    <th width="35%">Produk</th>
                                    <th width="15%">Satuan</th>
                                    <th width="15%">Harga Beli (Rp)</th>
                                    <th width="12%">Qty Pesan</th>
                                    <th width="18%">Subtotal (Rp)</th>
                                    <th width="5%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="baris-item-container">
                                <!-- Baris pertama default -->
                                <tr class="item-row">
                                    <td>
                                        <select name="produk_id[]" class="form-select select-produk" required
                                            onchange="pilihProduk(this)">
                                            <option value="">-- Pilih Produk --</option>
                                            @foreach($produks as $p)
                                                <option value="{{ $p->id }}" data-harga="{{ (int) $p->harga_beli }}"
                                                    data-satuan="{{ $p->satuanDasar->nama_satuan ?? '-' }}">
                                                    {{ $p->kode_produk }} - {{ $p->nama_produk }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control label-satuan" readonly placeholder="-">
                                    </td>
                                    <td>
                                        <input type="number" name="harga_satuan[]" class="form-control input-harga" required
                                            min="0" oninput="hitungSubtotal(this)">
                                    </td>
                                    <td>
                                        <input type="number" name="qty[]" class="form-control input-qty" required min="1"
                                            value="1" oninput="hitungSubtotal(this)">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control text-end input-subtotal" readonly value="0">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="hapusBaris(this)"
                                            disabled>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end fs-5">TOTAL PEMBELIAN:</th>
                                    <th class="text-end fs-5 text-primary fw-bold" id="label-total-akhir">Rp 0</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <a href="{{ route('admin.pembelian.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-success px-4 fw-bold">
                            <i class="bi bi-cart-check"></i> Simpan Pesanan Pembelian (PO)
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Data list produk untuk baris baru
        const optionsProduk = `
            <option value="">-- Pilih Produk --</option>
            @foreach($produks as $p)
                <option value="{{ $p->id }}" data-harga="{{ (int) $p->harga_beli }}" data-satuan="{{ $p->satuanDasar->nama_satuan ?? '-' }}">
                    {{ $p->kode_produk }} - {{ $p->nama_produk }}
                </option>
            @endforeach
        `;

        function tambahBarisItem() {
            const tr = document.createElement('tr');
            tr.className = 'item-row';
            tr.innerHTML = `
                <td>
                    <select name="produk_id[]" class="form-select select-produk" required onchange="pilihProduk(this)">
                        ${optionsProduk}
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
                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusBaris(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            document.getElementById('baris-item-container').appendChild(tr);
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
                if (btnHapus) {
                    btnHapus.disabled = (rows.length === 1);
                }
            });
        }

        function pilihProduk(selectEl) {
            const row = selectEl.closest('tr');
            const selectedOption = selectEl.options[selectEl.selectedIndex];
            const harga = selectedOption.getAttribute('data-harga') || 0;
            const satuan = selectedOption.getAttribute('data-satuan') || '-';

            row.querySelector('.input-harga').value = harga;
            row.querySelector('.label-satuan').value = satuan;
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