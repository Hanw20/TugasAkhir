@extends('layouts.adminlte')

@section('title', 'Data Supplier')
@section('page_title', 'Kelola Data Supplier')

@section('content')
 <div class="row">
 <div class="col-12">
 <button class="btn mb-3 text-white" style="background-color: #2A9D8F; font-weight: 500; padding: 7px 16px;" onclick="tambahData()">
 Tambah Supplier
 </button>

 <div class="card">
 <div class="card-body table-responsive">
 <table class="table table-bordered table-striped align-middle">
 <thead>
 <tr>
 <th width="5%" class="text-center">No</th>
 <th>Kode</th>
 <th>Nama Supplier</th>
 <th>No. Telepon</th>
 <th>Alamat</th>
 <th width="180px">Aksi</th>
 </tr>
 </thead>
 <tbody>
 @foreach($data as $index => $row)
 <tr>
 <td class="text-center">{{ $index + 1 }}</td>
 <td>{{ $row->kode_supplier }}</td>
 <td>{{ $row->nama_supplier }}</td>
 <td>{{ $row->nomor_telepon }}</td>
 <td>{{ $row->alamat }}</td>
 <td>
 <div class="d-flex gap-1">
 <a href="/admin/supplier/{{ $row->id }}" class="btn btn-sm" style="background-color: #E0F2FE; color: #0369A1; font-weight: 500; font-size: 0.8rem; padding: 4px 10px;">Detail</a>
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

 <!-- ================= BOOTSTRAP 5 MODAL SUPPLIER ================= -->
 <div class="modal fade" id="modal-form" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered">
 <div class="modal-content border-0" >
 <form id="form-supplier" method="POST">
 @csrf
 <input type="hidden" name="_method" id="method" value="POST">
 <input type="hidden" id="supplier_id">

 <div class="modal-header py-3 border-0">
 <div>
 <h5 class="modal-title fs-5 fw-bold" id="modal-title" style="color: #264653;">
 Tambah Supplier
 </h5>
 <small class="text-muted">Isi data identitas vendor distributor / supplier barang</small>
 </div>
 <button type="button" class="btn-close" data-bs-dismiss="modal"
 aria-label="Close"></button>
 </div>
 <div class="modal-body px-4 pt-1 pb-3">
 <div class="mb-3">
 <label for="kode_supplier" class="form-label fw-semibold">Kode Supplier <span
 class="text-danger">*</span></label>
 <input type="text" name="kode_supplier" id="kode_supplier" class="form-control"
 placeholder="Contoh: SUP-001" required>
 </div>

 <div class="mb-3">
 <label for="nama_supplier" class="form-label fw-semibold">Nama Supplier <span
 class="text-danger">*</span></label>
 <input type="text" name="nama_supplier" id="nama_supplier" class="form-control"
 placeholder="Contoh: PT Semen Indonesia Distributor" required>
 </div>

 <div class="mb-3">
 <label for="nomor_telepon" class="form-label fw-semibold">Nomor Telepon / WhatsApp <span
 class="text-danger">*</span></label>
 <input type="text" name="nomor_telepon" id="nomor_telepon" class="form-control"
 placeholder="Contoh: 081234567890" required>
 </div>

 <div class="mb-0">
 <label for="alamat" class="form-label fw-semibold">Alamat Lengkap</label>
 <textarea name="alamat" id="alamat" class="form-control" rows="3"
 placeholder="Masukkan alamat kantor / gudang supplier..."></textarea>
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
 $('#form-supplier')[0].reset();
 $('#method').val('POST');
 $('#modal-title').text('Tambah Supplier');
 $('#modal-form').modal('show');
 }

 function editData(id) {
 $.get('/admin/supplier/' + id + '/edit', function (data) {
 $('#modal-title').text('Edit Supplier');
 $('#method').val('PUT');
 $('#supplier_id').val(id);

 // Mengisi banyak kolom inputan sekaligus
 $('#kode_supplier').val(data.kode_supplier);
 $('#nama_supplier').val(data.nama_supplier);
 $('#nomor_telepon').val(data.nomor_telepon);
 $('#email').val(data.email);
 $('#alamat').val(data.alamat);

 $('#modal-form').modal('show');
 });
 }

 function deleteData(id) {
 if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
 $.ajax({
 url: '/admin/supplier/' + id,
 type: 'POST',
 data: { '_method': 'DELETE' },
 success: function (response) {
 alert(response.message);
 location.reload();
 }
 });
 }
 }

 $('#form-supplier').on('submit', function (e) {
 e.preventDefault();

 let id = $('#supplier_id').val();
 let url = $('#method').val() === 'POST' ? '/admin/supplier' : '/admin/supplier/' + id;

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
 // Laravel akan mengirim error otomatis jika Kode Supplier kembar
 alert('Gagal menyimpan! Pastikan Kode Supplier belum dipakai dan form terisi dengan benar.');
 }
 });
 });
 </script>
@endpush