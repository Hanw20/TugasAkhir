@extends('layouts.adminlte')

@section('title', 'Master Konversi')
@section('page_title', 'Kelola Rumus Konversi Satuan')

@section('content')
 <div class="row">
 <div class="col-12">
 <button class="btn mb-3 text-white" style="background-color: #2A9D8F; font-weight: 500; padding: 7px 16px;" onclick="tambahData()">
 Tambah Rumus Konversi
 </button>

 <!-- Kotak Bantuan / Petunjuk -->
 <div class="alert border-0" style="background-color: #EBF5F3; color: #264653;">
 <i class="bi bi-info-circle me-1" style="color: #2A9D8F;"></i> <strong>Cara Membaca Rumus:</strong> 1 [Satuan Asal] = [Nilai Konversi]
 [Satuan Tujuan]. <br>
 <em>Contoh: 1 <strong>Dus</strong> = 12 <strong>Pcs</strong>, atau 1 <strong>Sak</strong> = 50
 <strong>Kg</strong>.</em>
 </div>

 <div class="card" >
 <div class="card-body table-responsive">
 <table class="table table-bordered table-striped align-middle">
 <thead>
 <tr>
 <th width="5%">No</th>
 <th>Satuan Asal (1)</th>
 <th>Satuan Tujuan</th>
 <th>Nilai Konversi</th>
 <th>Cara Baca Rumus</th>
 <th width="150px">Aksi</th>
 </tr>
 </thead>
 <tbody>
 @foreach($data as $index => $row)
 <tr>
 <td>{{ $index + 1 }}</td>
 <td>{{ $row->satuanAsal->nama_satuan ?? '-' }}</td>
 <td>{{ $row->satuanTujuan->nama_satuan ?? '-' }}</td>
 <td>{{ (float) $row->nilai_konversi }}</td>
 <td>
 <!-- Menampilkan cara baca rumus secara visual -->
 <span class="badge" style="background-color: #F1F5F9; color: #475569; font-size: 0.85rem;">
 1 {{ $row->satuanAsal->nama_satuan ?? '' }} = {{ (float) $row->nilai_konversi }}
 {{ $row->satuanTujuan->nama_satuan ?? '' }}
 </span>
 </td>
 <td>
 <div class="d-flex gap-1">
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

 <!-- ================= BOOTSTRAP 5 MODAL MASTER KONVERSI ================= -->
 <div class="modal fade" id="modal-form" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered">
 <div class="modal-content border-0" >
 <form id="form-konversi" method="POST">
 @csrf
 <input type="hidden" name="_method" id="method" value="POST">
 <input type="hidden" id="konversi_id">

 <div class="modal-header py-3 border-0">
 <div>
 <h5 class="modal-title fs-5 fw-bold" id="modal-title" style="color: #264653;">
 Tambah Rumus Konversi
 </h5>
 <small class="text-muted">Tentukan rasio konversi satuan besar ke satuan kecil</small>
 </div>
 <button type="button" class="btn-close" data-bs-dismiss="modal"
 aria-label="Close"></button>
 </div>
 <div class="modal-body px-4 pt-1 pb-3">
 <div class="mb-3">
 <label for="satuan_asal_id" class="form-label fw-semibold">1 Satuan Asal (Kemasan Besar) <span
 class="text-danger">*</span></label>
 <select name="satuan_asal_id" id="satuan_asal_id" class="form-select" required>
 <option value="">Pilih Satuan Asal</option>
 @foreach($satuan as $s)
 <option value="{{ $s->id }}">{{ $s->nama_satuan }}</option>
 @endforeach
 </select>
 </div>

 <div class="mb-3">
 <label for="nilai_konversi" class="form-label fw-semibold">Setara Nilai Konversi <span
 class="text-danger">*</span></label>
 <input type="number" step="any" name="nilai_konversi" id="nilai_konversi" class="form-control"
 placeholder="Contoh: 12 atau 50" required>
 </div>

 <div class="mb-0">
 <label for="satuan_tujuan_id" class="form-label fw-semibold">Satuan Tujuan (Eceran) <span
 class="text-danger">*</span></label>
 <select name="satuan_tujuan_id" id="satuan_tujuan_id" class="form-select" required>
 <option value="">Pilih Satuan Tujuan</option>
 @foreach($satuan as $s)
 <option value="{{ $s->id }}">{{ $s->nama_satuan }}</option>
 @endforeach
 </select>
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
 $('#form-konversi')[0].reset();
 $('#method').val('POST');
 $('#modal-title').text('Tambah Rumus Konversi');
 $('#modal-form').modal('show');
 }

 function editData(id) {
 $.get('/admin/konversi/' + id + '/edit', function (data) {
 $('#modal-title').text('Edit Rumus Konversi');
 $('#method').val('PUT');
 $('#konversi_id').val(id);

 $('#satuan_asal_id').val(data.satuan_asal_id);
 $('#satuan_tujuan_id').val(data.satuan_tujuan_id);
 $('#nilai_konversi').val(parseFloat(data.nilai_konversi));

 $('#modal-form').modal('show');
 });
 }

 function deleteData(id) {
 if (confirm('Apakah Anda yakin ingin menghapus rumus ini?')) {
 $.ajax({
 url: '/admin/konversi/' + id,
 type: 'POST',
 data: { '_method': 'DELETE' },
 success: function (response) {
 alert(response.message);
 location.reload();
 }
 });
 }
 }

 $('#form-konversi').on('submit', function (e) {
 e.preventDefault();
 let id = $('#konversi_id').val();
 let url = $('#method').val() === 'POST' ? '/admin/konversi' : '/admin/konversi/' + id;

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
 // Menangkap pesan error jika Satuan Asal = Satuan Tujuan (aturan validasi di Controller)
 let errorMsg = 'Gagal menyimpan!';
 if (err.responseJSON && err.responseJSON.errors && err.responseJSON.errors.satuan_tujuan_id) {
 errorMsg = err.responseJSON.errors.satuan_tujuan_id[0];
 }
 alert(errorMsg);
 }
 });
 });
 </script>
@endpush