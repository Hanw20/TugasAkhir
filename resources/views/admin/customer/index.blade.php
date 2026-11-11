@extends('layouts.adminlte')

@section('title', 'Data Customer')
@section('page_title', 'Kelola Data Customer')

@section('content')
 <div class="row">
 <div class="col-12">
 <button class="btn mb-3 text-white" style="background-color: #2A9D8F; font-weight: 500; padding: 7px 16px;" onclick="tambahData()">
 Tambah Customer
 </button>

 <div class="card">
 <div class="card-body table-responsive">
 <table class="table table-bordered table-striped align-middle">
 <thead>
 <tr>
 <th width="5%" class="text-center">No</th>
 <th>Nama Customer</th>
 <th>No. Telepon</th>
 <th>Alamat</th>
 <th width="180px">Aksi</th>
 </tr>
 </thead>
 <tbody>
 @foreach($data as $index => $row)
 <tr>
 <td class="text-center">{{ $index + 1 }}</td>
 <td>{{ $row->nama_customer }}</td>
 <td>{{ $row->no_telepon }}</td>
 <td>{{ $row->alamat }}</td>
 <td>
 <div class="d-flex gap-1">
 <a href="/admin/customer/{{ $row->id }}" class="btn btn-sm" style="background-color: #E0F2FE; color: #0369A1; font-weight: 500; font-size: 0.8rem; padding: 4px 10px;">Detail</a>
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

 <!-- ================= BOOTSTRAP 5 MODAL ================= -->
 <div class="modal fade" id="modal-form" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered">
 <div class="modal-content border-0" >
 <form id="form-customer" method="POST">
 @csrf
 <input type="hidden" name="_method" id="method" value="POST">
 <input type="hidden" id="customer_id">

 <div class="modal-header py-3 border-0">
 <div>
 <h5 class="modal-title fs-5 fw-bold" id="modal-title" style="color: #264653;">
 Tambah Customer
 </h5>
 <small class="text-muted">Isi data identitas pelanggan dan kontak pengiriman</small>
 </div>
 <button type="button" class="btn-close" data-bs-dismiss="modal"
 aria-label="Close"></button>
 </div>

 <div class="modal-body px-4 pt-1 pb-3">
 <div class="mb-3">
 <label for="nama_customer" class="form-label fw-semibold">Nama Customer <span
 class="text-danger">*</span></label>
 <input type="text" name="nama_customer" id="nama_customer" class="form-control"
 placeholder="Contoh: Pak Budi / UD Jaya Mandiri" required>
 </div>

 <div class="mb-3">
 <label for="no_telepon" class="form-label fw-semibold">Nomor Telepon / WhatsApp</label>
 <input type="text" name="no_telepon" id="no_telepon" class="form-control"
 placeholder="Contoh: 08123456789">
 </div>

 <div class="mb-0">
 <label for="alamat" class="form-label fw-semibold">Alamat Lengkap</label>
 <textarea name="alamat" id="alamat" class="form-control" rows="3"
 placeholder="Masukkan alamat pengiriman / proyek..."></textarea>
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
 $('#form-customer')[0].reset();
 $('#method').val('POST');
 $('#modal-title').text('Tambah Customer');
 $('#modal-form').modal('show');
 }

 function editData(id) {
 $.get('/admin/customer/' + id + '/edit', function (data) {
 $('#modal-title').text('Edit Customer');
 $('#method').val('PUT');
 $('#customer_id').val(id);

 $('#nama_customer').val(data.nama_customer);
 $('#no_telepon').val(data.no_telepon);
 $('#alamat').val(data.alamat);

 $('#modal-form').modal('show');
 });
 }

 function deleteData(id) {
 if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
 $.ajax({
 url: '/admin/customer/' + id,
 type: 'POST',
 data: { '_method': 'DELETE' },
 success: function (response) {
 alert(response.message);
 location.reload();
 }
 });
 }
 }

 $('#form-customer').on('submit', function (e) {
 e.preventDefault();

 let id = $('#customer_id').val();
 let url = $('#method').val() === 'POST' ? '/admin/customer' : '/admin/customer/' + id;

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
 alert('Gagal menyimpan! Pastikan form terisi dengan benar.');
 }
 });
 });
 </script>
@endpush