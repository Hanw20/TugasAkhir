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
                                <button onclick="deleteData({{ $row->id }})" class="btn btn-sm btn-danger">Hapus</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ================= BOOTSTRAP MODAL ================= -->
<div class="modal fade" id="modal-form" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-merek" method="POST">
                @csrf
                <input type="hidden" name="_method" id="method" value="POST">
                <input type="hidden" id="merek_id">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Tambah Merek</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Merek</label>
                        <input type="text" name="nama_merek" id="nama_merek" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
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
        $.get('/admin/merek/' + id + '/edit', function(data) {
            $('#modal-title').text('Edit Merek');
            $('#method').val('PUT');
            $('#merek_id').val(id);
            $('#nama_merek').val(data.nama_merek);
            $('#modal-form').modal('show');
        });
    }

    function deleteData(id) {
        if(confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            $.ajax({
                url: '/admin/merek/' + id,
                type: 'POST',
                data: { '_method': 'DELETE' },
                success: function(response) {
                    alert(response.message);
                    location.reload();
                }
            });
        }
    }

    $('#form-merek').on('submit', function(e) {
        e.preventDefault();
        
        let id = $('#merek_id').val();
        // Arahkan ke URL /admin/merek
        let url = $('#method').val() === 'POST' ? '/admin/merek' : '/admin/merek/' + id;

        $.ajax({
            url: url,
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#modal-form').modal('hide');
                alert(response.message);
                location.reload();
            },
            error: function(err) {
                alert('Gagal menyimpan! Pastikan nama merek terisi.');
            }
        });
    });
</script>
@endpush