@extends('layouts.adminlte')

@section('title', 'Input Stok Opname Baru')
@section('page_title', 'Form Input Stok Opname Fisik Gudang')

@section('content')
<div class="container-fluid">
    <form action="{{ route('admin.stok-opname.store') }}" method="POST">
        @csrf
        <div class="card card-outline card-primary shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title fw-bold m-0"><i class="bi bi-pencil-square me-2"></i>Informasi Pemeriksaan</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Tanggal Pemeriksaan</label>
                        <input type="datetime-local" name="tanggal" class="form-control" value="{{ date('Y-m-d\TH:i') }}" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Catatan / Alasan Opname</label>
                        <input type="text" name="catatan" class="form-control" placeholder="Contoh: Audit stok akhir bulan / Penyesuaian barang rusak di rak B">
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-outline card-success shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold m-0"><i class="bi bi-boxes me-2"></i>Daftar Item yang Dihitung Fisik</h5>
                <button type="button" class="btn btn-sm btn-success fw-bold" id="btnTambahBarang">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Baris Produk
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle m-0" id="tabelOpname">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 40%;">Pilih Produk</th>
                                <th style="width: 20%;">Stok di Komputer</th>
                                <th style="width: 25%;">Stok Fisik di Rak</th>
                                <th style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Baris pertama default -->
                            <tr>
                                <td>
                                    <select name="items[0][produk_id]" class="form-select select-produk" required onchange="updateStokSistem(this)">
                                        <option value="">-- Pilih Produk --</option>
                                        @foreach($produk as $p)
                                            <option value="{{ $p->id }}" data-stok="{{ $p->stok }}"data-satuan="{{ $p->satuanDasar->nama_satuan ?? '' }}">
                                                [{{ $p->kode_produk }}] {{ $p->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-center font-monospace">
                                    <span class="stok-sistem-text fw-bold text-muted">-</span>
                                </td>
                                <td>
                                    <input type="number" step="any" min="0" name="items[0][stok_fisik]" class="form-control text-center fw-bold" placeholder="Jumlah fisik" required>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusBaris(this)"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('admin.stok-opname.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-success fw-bold px-4">
                    <i class="bi bi-check2-circle me-1"></i> Simpan & Perbarui Stok Gudang
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let barisIndex = 1;

    function updateStokSistem(selectElement) {
        let opt = selectElement.options[selectElement.selectedIndex];
        let stok = opt.getAttribute('data-stok') || '-';
        let satuan = opt.getAttribute('data-satuan') || '';
        let row = selectElement.closest('tr');
        row.querySelector('.stok-sistem-text').innerText = (stok !== '-') ? (stok + ' ' + satuan) : '-';
    }

    document.getElementById('btnTambahBarang').addEventListener('click', function() {
        let tbody = document.querySelector('#tabelOpname tbody');
        let optionsProduk = `@foreach($produk as $p)<option value="{{ $p->id }}" data-stok="{{ $p->stok }}" data-satuan="{{ $p->satuanDasar->nama_satuan ?? '' }}">[{{ $p->kode_produk }}] {{ $p->nama_produk }}</option>@endforeach`;

        let tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <select name="items[${barisIndex}][produk_id]" class="form-select select-produk" required onchange="updateStokSistem(this)">
                    <option value="">-- Pilih Produk --</option>
                    ${optionsProduk}
                </select>
            </td>
            <td class="text-center font-monospace">
                <span class="stok-sistem-text fw-bold text-muted">-</span>
            </td>
            <td>
                <input type="number" step="any" min="0" name="items[${barisIndex}][stok_fisik]" class="form-control text-center fw-bold" placeholder="Jumlah fisik" required>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusBaris(this)"><i class="bi bi-trash"></i></button>
            </td>
        `;
        tbody.appendChild(tr);
        barisIndex++;
    });

    function hapusBaris(btn) {
        let tbody = document.querySelector('#tabelOpname tbody');
        if (tbody.rows.length > 1) {
            btn.closest('tr').remove();
        } else {
            alert('Minimal harus ada 1 produk yang diperiksa!');
        }
    }
</script>
@endpush