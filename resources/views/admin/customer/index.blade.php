@extends('layouts.adminlte')

@section('title', 'Data Customer')
@section('page_title', 'Kelola Data Customer')

@section('content')
    <div class="row">
        <div class="col-12">
            <button class="btn btn-primary mb-3" onclick="tambahData()">
                <i class="fas fa-plus"></i> Tambah Customer
            </button>

            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Customer</th>
                                <th>No. Telepon</th>
                                <th>Alamat</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $row->nama_customer }}</td>
                                    <td>{{ $row->no_telepon }}</td>
                                    <td>{{ $row->alamat }}</td>
                                    <td>
                                        <a href="/admin/customer/{{ $row->id }}" class="btn btn-sm btn-info">Detail</a>
                                        <button onclick="editData({{ $row->id }})" class="btn btn-sm btn-warning">Edit</button>
                                        <button onclick="deleteData({{ $row->id }})"
                                            class="btn btn-sm btn-danger">Hapus</button>
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
            <div class="modal-content shadow border-0">
                <form id="form-customer" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="method" value="POST">
                    <input type="hidden" id="customer_id">

                    <div class="modal-header bg-primary text-white py-3">
                        <h5 class="modal-title fs-5 fw-bold" id="modal-title">
                            <i class="bi bi-person-plus me-2"></i> Tambah Customer
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4">
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

                    <div class="modal-footer bg-light py-2">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="bi bi-save me-1"></i> Simpan
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