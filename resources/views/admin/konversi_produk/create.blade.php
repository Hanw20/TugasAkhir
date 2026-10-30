@extends('layouts.adminlte')

@section('title', 'Konversi Produk Baru')
@section('page_title', 'Form Konversi Kemasan Satuan Produk')

@section('content')
<div class="container-fluid">
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header">
            <h5 class="card-title fw-bold m-0"><i class="bi bi-arrow-left-right me-2"></i>Proses Pemecahan / Repacking Barang</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.konversi-produk.store') }}" method="POST">
                @csrf
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Tanggal Konversi</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Pilih Rumus Master Konversi</label>
                        <select name="master_konversi_id" id="master_konversi_id" class="form-select" required onchange="updateRumus(this)">
                            <option value="">-- Pilih Rumus Konversi --</option>
                            @foreach($rumus as $r)
                                <option value="{{ $r->id }}" data-faktor="{{ (float)$r->nilai_konversi }}" data-asal="{{ $r->satuanAsal->nama_satuan ?? '' }}" data-tujuan="{{ $r->satuanTujuan->nama_satuan ?? '' }}">
                                    1 {{ $r->satuanAsal->nama_satuan ?? '' }} = {{ (float)$r->nilai_konversi }} {{ $r->satuanTujuan->nama_satuan ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-3 align-items-center mb-4">
                    <!-- Produk Asal -->
                    <div class="col-md-5">
                        <div class="p-3 border rounded bg-danger-subtle border-danger">
                            <h6 class="fw-bold text-danger mb-2"><i class="bi bi-box-arrow-up-right me-1"></i> Produk Asal (Stok Berkurang)</h6>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Pilih Produk Asal</label>
                                <select name="produk_asal_id" class="form-select" required onchange="cekStokAsal(this)">
                                    <option value="">-- Pilih Produk Asal --</option>
                                    @foreach($produk as $p)
                                        <option value="{{ $p->id }}" data-stok="{{ $p->stok }}" data-satuan="{{ $p->satuan->nama_satuan ?? '' }}">
                                            [{{ $p->kode_produk }}] {{ $p->nama_produk }} (Stok: {{ $p->stok }} {{ $p->satuan->nama_satuan ?? '' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label small fw-bold">Jumlah yang Dipakai</label>
                                <input type="number" step="any" min="0.01" name="qty_asal_dipakai" id="qty_asal" class="form-control fw-bold" placeholder="Misal: 1" required oninput="hitungOtomatis()">
                            </div>
                        </div>
                    </div>

                    <!-- Tanda Panah -->
                    <div class="col-md-2 text-center">
                        <i class="bi bi-arrow-right fs-1 text-primary d-none d-md-inline"></i>
                        <div class="small fw-bold text-muted mt-1" id="labelFaktor">Faktor: -</div>
                    </div>

                    <!-- Produk Tujuan -->
                    <div class="col-md-5">
                        <div class="p-3 border rounded bg-success-subtle border-success">
                            <h6 class="fw-bold text-success mb-2"><i class="bi bi-box-arrow-in-down-left me-1"></i> Produk Hasil (Stok Bertambah)</h6>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Pilih Produk Tujuan</label>
                                <select name="produk_tujuan_id" class="form-select" required>
                                    <option value="">-- Pilih Produk Hasil --</option>
                                    @foreach($produk as $p)
                                        <option value="{{ $p->id }}">
                                            [{{ $p->kode_produk }}] {{ $p->nama_produk }} (Stok Saat Ini: {{ $p->stok }} {{ $p->satuan->nama_satuan ?? '' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label small fw-bold">Hasil Aktual</label>
                                <input type="number" step="any" min="0.01" name="qty_hasil_aktual" id="qty_hasil" class="form-control fw-bold" placeholder="Hasil dihitung otomatis" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Catatan Konversi</label>
                    <input type="text" name="catatan" class="form-control" placeholder="Contoh: Memecah 1 sak semen 50kg menjadi eceran kantong 1kg">
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.konversi-produk.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary fw-bold px-4">
                        <i class="bi bi-check2-circle me-1"></i> Proses Konversi Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let faktorKonversi = 0;

    function updateRumus(select) {
        let opt = select.options[select.selectedIndex];
        faktorKonversi = parseFloat(opt.getAttribute('data-faktor')) || 0;
        let asal = opt.getAttribute('data-asal') || '';
        let tujuan = opt.getAttribute('data-tujuan') || '';

        document.getElementById('labelFaktor').innerText = faktorKonversi > 0 ? ('1 ' + asal + ' = ' + faktorKonversi + ' ' + tujuan) : 'Faktor: -';
        hitungOtomatis();
    }

    function hitungOtomatis() {
        let qtyAsal = parseFloat(document.getElementById('qty_asal').value) || 0;
        if (faktorKonversi > 0 && qtyAsal > 0) {
            document.getElementById('qty_hasil').value = qtyAsal * faktorKonversi;
        }
    }
</script>
@endpush