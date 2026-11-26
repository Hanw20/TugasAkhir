@extends('layouts.adminlte')

@section('title', 'Merek Produk')
@section('page_title', 'Kelola Merek Produk')

@section('content')
    <div class="row">
        <div class="col-12">
            <button class="btn mb-3 text-white" style="background-color: #2A9D8F; font-weight: 500; padding: 7px 16px;"
                onclick="tambahData()">
                Tambah Merek
            </button>

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-striped align-middle" id="tabel-merek">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Merek</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $index => $row)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $row->nama_merek }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="/admin/merek/{{ $row->id }}" class="btn btn-sm"
                                                style="background-color: #E0F2FE; color: #0369A1; font-weight: 500; font-size: 0.8rem; padding: 4px 10px;">Detail</a>
                                            @if($row->produk_count > 0)
                                                <button type="button" class="btn btn-sm" disabled
                                                    style="background-color: #E2E8F0; color: #94A3B8; font-weight: 500; font-size: 0.8rem; padding: 4px 10px; cursor: not-allowed;"
                                                    title="Terkunci: sedang digunakan oleh {{ $row->produk_count }} produk aktif">Edit</button>
                                                <button type="button" class="btn btn-sm" disabled
                                                    style="background-color: #E2E8F0; color: #94A3B8; font-weight: 500; font-size: 0.8rem; padding: 4px 10px; cursor: not-allowed;"
                                                    title="Terkunci: sedang digunakan oleh {{ $row->produk_count }} produk aktif">Hapus</button>
                                            @else
                                                <button onclick="editData({{ $row->id }})" class="btn btn-sm"
                                                    style="background-color: #FEF3C7; color: #B45309; font-weight: 500; font-size: 0.8rem; padding: 4px 10px;">Edit</button>
                                                <button onclick="deleteData({{ $row->id }})" class="btn btn-sm"
                                                    style="background-color: #FEE2E2; color: #B91C1C; font-weight: 500; font-size: 0.8rem; padding: 4px 10px;">Hapus</button>
                                            @endif
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

    <div class="modal fade" id="modal-form" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <form id="form-merek" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="method" value="POST">
                    <input type="hidden" id="merek_id">

                    <div class="modal-header py-3 border-0">
                        <div>
                            <h5 class="modal-title fs-5 fw-bold" id="modal-title" style="color: #264653;">
                                Tambah Merek
                            </h5>
                            <small class="text-muted">Isi nama merek atau brand produk bahan bangunan</small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 pt-1 pb-3">
                        <div class="mb-0">
                            <label for="nama_merek" class="form-label fw-semibold">Nama Merek <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nama_merek" id="nama_merek" class="form-control"
                                placeholder="Contoh: Semen Gresik, Dulux, Rucika" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-sm"
                            style="background-color: #E2E8F0; color: #334155; font-weight: 500; padding: 6px 16px;"
                            data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-sm text-white fw-bold"
                            style="background-color: #2A9D8F; padding: 6px 18px;">
                            <i class="bi bi-download me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $(document).ready(function () {
            $('#tabel-merek').DataTable({
                columnDefs: [{ targets: [2], orderable: false, searchable: false }]
            });
        });

        function tambahData() {
            $('#form-merek')[0].reset();
            $('#method').val('POST');
            $('#modal-title').text('Tambah Merek');
            $('#modal-form').modal('show');
        }

        function editData(id) {
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
                    },
                    error: function (xhr) {
                        let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Terjadi kesalahan saat menghapus data.';
                        alert(msg);
                    }
                });
            }
        }

        $('#form-merek').on('submit', function (e) {
            e.preventDefault();

            let id = $('#merek_id').val();
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
                error: function (xhr) {
                    let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Gagal menyimpan! Pastikan nama merek terisi.';
                    alert(msg);
                }
            });
        });
    </script>
@endpush
