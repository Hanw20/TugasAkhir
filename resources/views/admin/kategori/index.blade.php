@extends('layouts.adminlte')

@section('title', 'Kategori Produk')
@section('page_title', 'Kelola Kategori Produk')

@section('content')
    <div class="row">
        <div class="col-12">
            <button class="btn mb-3 text-white" style="background-color: #2A9D8F; font-weight: 500; padding: 7px 16px;"
                onclick="tambahData()">
                Tambah Kategori
            </button>

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>Nama Kategori</th>
                                <th width="180px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $index => $row)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $row->nama_kategori }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="/admin/kategori/{{ $row->id }}" class="btn btn-sm"
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

    <!-- ================= BOOTSTRAP 5 MODAL KATEGORI ================= -->
    <div class="modal fade" id="modal-form" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <form id="form-kategori" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="method" value="POST">
                    <input type="hidden" id="kategori_id">

                    <div class="modal-header py-3 border-0">
                        <div>
                            <h5 class="modal-title fs-5 fw-bold" id="modal-title" style="color: #264653;">
                                Tambah Kategori
                            </h5>
                            <small class="text-muted">Isi nama kelompok kategori produk bahan bangunan</small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 pt-1 pb-3">
                        <div class="mb-0">
                            <label for="nama_kategori" class="form-label fw-semibold">Nama Kategori <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nama_kategori" id="nama_kategori" class="form-control"
                                placeholder="Contoh: Semen, Besi, Cat, Keramik" required>
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

<!-- ================= JAVASCRIPT AJAX ================= -->
@push('scripts')
    <script>
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        function tambahData() {
            $('#form-kategori')[0].reset();
            $('#method').val('POST');
            $('#modal-title').text('Tambah Kategori');
            $('#modal-form').modal('show');
        }

        function editData(id) {
            $.get('/admin/kategori/' + id + '/edit', function (data) {
                $('#modal-title').text('Edit Kategori');
                $('#method').val('PUT');
                $('#kategori_id').val(id);
                $('#nama_kategori').val(data.nama_kategori);
                $('#modal-form').modal('show');
            });
        }

        function deleteData(id) {
            if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                $.ajax({
                    url: '/admin/kategori/' + id,
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

        $('#form-kategori').on('submit', function (e) {
            e.preventDefault();

            let id = $('#kategori_id').val();
            let url = $('#method').val() === 'POST' ? '/admin/kategori' : '/admin/kategori/' + id;

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
                    let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Gagal menyimpan data!';
                    alert(msg);
                }
            });
        });
    </script>
@endpush
