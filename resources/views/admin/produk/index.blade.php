@extends('layouts.adminlte')

@section('title', 'Data Produk')
@section('page_title', 'Kelola Katalog Produk')

@section('content')
 <div class="row">
 <div class="col-12">
 <button class="btn mb-3 text-white" style="background-color: #2A9D8F; font-weight: 500; padding: 7px 16px;" onclick="tambahData()">
 Tambah Produk
 </button>

 <div class="card">
 <div class="card-body table-responsive">
 <table class="table table-bordered table-striped align-middle">
 <thead>
 <tr>
 <th width="5%" class="text-center">No</th>
 <th>Kode</th>
 <th>Nama Produk</th>
 <th>Kategori</th>
 <th>Merek</th>
 <th>Harga Jual</th>
 <th>Stok</th>
 <th width="180px">Aksi</th>
 </tr>
 </thead>
 <tbody>
 @foreach($data as $index => $row)
 <tr>
 <td>{{ $index + 1 }}</td>
 <td>{{ $row->kode_produk }}</td>
 <td>{{ $row->nama_produk }}</td>
 <td>{{ $row->kategori->nama_kategori ?? '-' }}</td>
 <td>{{ $row->merek->nama_merek ?? '-' }}</td>
 <!-- Format Uang Rupiah Otomatis -->
 <td>Rp {{ number_format($row->harga_jual, 0, ',', '.') }}</td>
 <td>{{ $row->stok }} {{ $row->satuanDasar->nama_satuan ?? '' }}</td>
 <td>
 <!-- Tombol Aksi Pastel Modern Sesuai Figma -->
 <div class="d-flex gap-1">
 <a href="/admin/produk/{{ $row->id }}" class="btn btn-sm" style="background-color: #E0F2FE; color: #0369A1; font-weight: 500; font-size: 0.8rem; padding: 4px 10px;">Detail</a>
 <button onclick="editData({{ $row->id }})" class="btn btn-sm" style="background-color: #FEF3C7; color: #B45309; font-weight: 500; font-size: 0.8rem; padding: 4px 10px;">Edit</button>
 <button onclick="deleteData({{ $row->id }})" class="btn btn-sm" style="background-color: #FEE2E2; color: #B91C1C; font-weight: 500; font-size: 0.8rem; padding: 4px 10px;">Hapus</button>
 </div>
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 </div>
 </div>
 </div>

 <!-- ================= BOOTSTRAP 5 MODAL PRODUK ================= -->
 <div class="modal fade" id="modal-form" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
 <div class="modal-dialog modal-lg modal-dialog-centered">
 <div class="modal-content border-0" >
 <form id="form-produk" method="POST">
 @csrf
 <input type="hidden" name="_method" id="method" value="POST">
 <input type="hidden" id="produk_id">

 <div class="modal-header py-3 border-0">
 <div>
 <h5 class="modal-title fs-5 fw-bold" id="modal-title" style="color: #264653;">
 Tambah Produk
 </h5>
 <small class="text-muted">isi informasi produk, harga dasar, dan batas stok minimum.</small>
 </div>
 <button type="button" class="btn-close" data-bs-dismiss="modal"
 aria-label="Close"></button>
 </div>
 <div class="modal-body px-4 pt-1 pb-3">
 <div class="row g-3 mb-3">
 <div class="col-md-6">
 <label for="kode_produk" class="form-label fw-semibold">Kode Produk <span
 class="text-danger">*</span></label>
 <input type="text" name="kode_produk" id="kode_produk" class="form-control"
 placeholder="PRD-SMN01" required>
 </div>
 <div class="col-md-6">
 <label for="nama_produk" class="form-label fw-semibold">Nama Produk <span
 class="text-danger">*</span></label>
 <input type="text" name="nama_produk" id="nama_produk" class="form-control"
 placeholder="Semen Gresik 40 Kg" required>
 </div>
 </div>

 <div class="row g-3 mb-3">
 <div class="col-md-4">
 <label for="kategori_produk_id" class="form-label fw-semibold">Kategori <span
 class="text-danger">*</span></label>
 <select name="kategori_produk_id" id="kategori_produk_id" class="form-select" required>
 <option value="">Pilih Kategori</option>
 @foreach($kategori as $k)
 <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
 @endforeach
 </select>
 </div>
 <div class="col-md-4">
 <label for="merek_id" class="form-label fw-semibold">Merek <span
 class="text-danger">*</span></label>
 <select name="merek_id" id="merek_id" class="form-select" required>
 <option value="">Pilih Merek</option>
 @foreach($merek as $m)
 <option value="{{ $m->id }}">{{ $m->nama_merek }}</option>
 @endforeach
 </select>
 </div>
 <div class="col-md-4">
 <label for="satuan_dasar_id" class="form-label fw-semibold">Satuan Dasar <span
 class="text-danger">*</span></label>
 <select name="satuan_dasar_id" id="satuan_dasar_id" class="form-select" required>
 <option value="">Pilih Satuan</option>
 @foreach($satuan as $s)
 <option value="{{ $s->id }}">{{ $s->nama_satuan }}</option>
 @endforeach
 </select>
 </div>
 </div>

 <div class="row g-3 mb-3">
 <div class="col-md-4">
 <label for="harga_beli" class="form-label fw-semibold">Harga Beli (Modal) <span
 class="text-danger">*</span></label>
 <div class="input-group">
 <span class="input-group-text bg-white border-end-0">Rp</span>
 <input type="number" step="any" name="harga_beli" id="harga_beli" class="form-control border-start-0"
 placeholder="0" required>
 </div>
 </div>
 <div class="col-md-4">
 <label for="harga_jual" class="form-label fw-semibold">Harga Jual <span
 class="text-danger">*</span></label>
 <div class="input-group">
 <span class="input-group-text bg-white border-end-0">Rp</span>
 <input type="number" step="any" name="harga_jual" id="harga_jual" class="form-control border-start-0"
 placeholder="0" required>
 </div>
 </div>
 <div class="col-md-4">
 <label for="metode_jual" class="form-label fw-semibold">Metode Jual <span
 class="text-danger">*</span></label>
 <select name="metode_jual" id="metode_jual" class="form-select" required>
 <option value="satuan">Satuan (Pcs/Sak/Dus)</option>
 <option value="nominal_timbang">Nominal Timbang (Paku/Kawat)</option>
 </select>
 </div>
 </div>

 <div class="row g-3">
 <div class="col-md-6">
 <label for="stok" class="form-label fw-semibold">Stok Awal <span
 class="text-danger">*</span></label>
 <input type="number" step="any" name="stok" id="stok" class="form-control" placeholder="0"
 required>
 </div>
 <div class="col-md-6">
 <label for="stok_minimum" class="form-label fw-semibold">Stok Minimum (Batas Peringatan)
 <span class="text-danger">*</span></label>
 <input type="number" step="any" name="stok_minimum" id="stok_minimum" class="form-control"
 placeholder="0" required>
 </div>
 </div>
 </div>
 <div class="modal-footer border-0 px-4 pb-4">
 <button type="button" class="btn btn-sm" style="background-color: #E2E8F0; color: #334155; font-weight: 500; padding: 6px 16px;" data-bs-dismiss="modal">
 <i class="bi bi-x-circle me-1"></i> Batal
 </button>
 <button type="submit" class="btn btn-sm text-white fw-bold" style="background-color: #2A9D8F; padding: 6px 18px;">
 <i class="bi bi-download me-1"></i> Simpan
 </button>
 </div>
 </form>
 </div>
 </div>
 </div>
@endsection

<!-- ================= JAVASCRIPT AJAX ================= -->
@push('scripts')
 <script>
 $.ajaxSetup({
 headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
 });

 function tambahData() {
 $('#form-produk')[0].reset();
 $('#method').val('POST');
 $('#modal-title').text('Tambah Produk');
 $('#modal-form').modal('show');
 }

 function editData(id) {
 $.get('/admin/produk/' + id + '/edit', function (data) {
 $('#modal-title').text('Edit Produk');
 $('#method').val('PUT');
 $('#produk_id').val(id);

 // Isi semua inputan
 $('#kode_produk').val(data.kode_produk);
 $('#nama_produk').val(data.nama_produk);
 $('#kategori_produk_id').val(data.kategori_produk_id);
 $('#merek_id').val(data.merek_id);
 $('#satuan_dasar_id').val(data.satuan_dasar_id);

 // Math.floor menghilangkan angka desimal bawaan database
 $('#harga_beli').val(Math.floor(data.harga_beli));
 $('#harga_jual').val(Math.floor(data.harga_jual));
 $('#stok').val(Math.floor(data.stok));
 $('#stok_minimum').val(Math.floor(data.stok_minimum));

 $('#modal-form').modal('show');
 });
 }

 function deleteData(id) {
 if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
 $.ajax({
 url: '/admin/produk/' + id,
 type: 'POST',
 data: { '_method': 'DELETE' },
 success: function (response) {
 alert(response.message);
 location.reload();
 }
 });
 }
 }

 $('#form-produk').on('submit', function (e) {
 e.preventDefault();
 let id = $('#produk_id').val();
 let url = $('#method').val() === 'POST' ? '/admin/produk' : '/admin/produk/' + id;

 $.ajax({
 url: url,
 type: 'POST',
 data: $(this).serialize(),
 success: function (response) {
 $('#modal-form').modal('hide');
 alert(response.message);
 location.reload();
 },
 error: function (err) {
 alert('Gagal menyimpan! Pastikan kode belum dipakai dan semua data wajib diisi.');
 }
 });
 });
 </script>
@endpush