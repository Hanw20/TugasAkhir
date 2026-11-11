@extends('layouts.adminlte')

@section('title', 'Satuan Produk')
@section('page_title', 'Kelola Satuan Produk')

@section('content')
    <div class="row">
        <div class="col-12">
            <button class="btn mb-3 text-white shadow-sm" style="background-color: #2A9D8F; border-radius: 8px; font-weight: 500; padding: 7px 16px;" onclick="tambahData()">
                Tambah Satuan
            </button>

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>Nama Satuan</th>
                                <th width="140px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $index => $row)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $row->nama_satuan }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button onclick="editData({{ $row->id }})" class="btn btn-sm" style="background-color: #FEF3C7; color: #B45309; border-radius: 6px; font-weight: 500; font-size: 0.8rem; padding: 4px 10px;">Edit</button>
                                            <button onclick="deleteData({{ $row->id }})" class="btn btn-sm" style="background-color: #FEE2E2; color: #B91C1C; border-radius: 6px; font-weight: 500; font-size: 0.8rem; padding: 4px 10px;">Hapus</button>
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

    <!-- ================= BOOTSTRAP 5 MODAL SATUAN ================= -->
    <div class="modal fade" id="modal-form" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow border-0" style="border-radius: 12px; overflow: hidden;">
                <form id="form-satuan" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="method" value="POST">
                    <input type="hidden" id="satuan_id">

                    <div class="modal-header py-3 border-0">
                        <div>
                            <h5 class="modal-title fs-5 fw-bold" id="modal-title" style="color: #264653;">
                                Tambah Satuan
                            </h5>
                            <small class="text-muted">Isi nama unit satuan barang (dasar atau eceran)</small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 pt-1 pb-3">
                        <div class="mb-0">
                            <label for="nama_satuan" class="form-label fw-semibold">Nama Satuan <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nama_satuan" id="nama_satuan" class="form-control"
                                placeholder="Contoh: Pcs, Kg, Sak, Dus, Batang" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-sm" style="background-color: #E2E8F0; color: #334155; font-weight: 500; padding: 6px 16px; border-radius: 6px;" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-sm text-white fw-bold shadow-sm" style="background-color: #2A9D8F; padding: 6px 18px; border-radius: 6px;">
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
            $('#form-satuan')[0].reset();
            $('#method').val('POST');
            $('#modal-title').text('Tambah Satuan');
            $('#modal-form').modal('show');
        }

        function editData(id) {
            $.get('/admin/satuan/' + id + '/edit', function (data) {
                $('#modal-title').text('Edit Satuan');
                $('#method').val('PUT');
                $('#satuan_id').val(id);
                $('#nama_satuan').val(data.nama_satuan);
                $('#modal-form').modal('show');
            });
        }

        function deleteData(id) {
            if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                $.ajax({
                    url: '/admin/satuan/' + id,
                    type: 'POST',
                    data: { '_method': 'DELETE' },
                    success: function (response) {
                        alert(response.message);
                        location.reload();
                    }
                });
            }
        }

        $('#form-satuan').on('submit', function (e) {
            e.preventDefault();

            let id = $('#satuan_id').val();
            let url = $('#method').val() === 'POST' ? '/admin/satuan' : '/admin/satuan/' + id;

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
                    alert('Gagal menyimpan! Cek kembali isian Anda.');
                }
            });
        });
    </script>
@endpush