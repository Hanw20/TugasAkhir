@extends('layouts.adminlte')

@section('title', 'Merek Produk')
@section('page_title', 'Kelola Merek Produk')

@section('content')
    <div class="row">
        <div class="col-12">
            <button class="btn btn-primary mb-3" onclick="tambahData()">
                <i class="fas fa-plus"></i> Tambah Merek
            </button>

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Merek</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $row->nama_merek }}</td>
                                    <td>
                                        <a href="/admin/merek/{{ $row->id }}" class="btn btn-sm btn-info">Detail</a>
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

    <!-- ================= BOOTSTRAP 5 MODAL MEREK ================= -->
    <div class="modal fade" id="modal-form" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow border-0">
                <form id="form-merek" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="method" value="POST">
                    <input type="hidden" id="merek_id">

                    <div class="modal-header bg-primary text-white py-3">
                        <h5 class="modal-title fs-5 fw-bold" id="modal-title">
                            <i class="bi bi-bookmark-check me-2"></i> Tambah Merek
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-0">
                            <label for="nama_merek" class="form-label fw-semibold">Nama Merek <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nama_merek" id="nama_merek" class="form-control"
                                placeholder="Contoh: Semen Gresik, Dulux, Rucika" required>
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
            $('#form-merek')[0].reset();
            $('#method').val('POST');
            $('#modal-title').text('Tambah Merek');
            $('#modal-form').modal('show');
        }

        function editData(id) {
            // Ambil data ke URL /admin/merek
            $.get('/admin/merek/' + id + '/edit', function (data) {
                $('#modal-title').text('Edit Merek');
                $('#method').val('PUT');
                $('#merek_id').val(id);
                $('#nama_merek').val(data.nama_merek);
                $('#modal-form').modal('show');
            });
        }

        function deleteData(id) {
            if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                $.ajax({
                    url: '/admin/merek/' + id,
                    type: 'POST',
                    data: { '_method': 'DELETE' },
                    success: function (response) {
                        alert(response.message);
                        location.reload();
                    }
                });
            }
        }

        $('#form-merek').on('submit', function (e) {
            e.preventDefault();

            let id = $('#merek_id').val();
            // Arahkan ke URL /admin/merek
            let url = $('#method').val() === 'POST' ? '/admin/merek' : '/admin/merek/' + id;

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
                    alert('Gagal menyimpan! Pastikan nama merek terisi.');
                }
            });
        });
    </script>
@endpush