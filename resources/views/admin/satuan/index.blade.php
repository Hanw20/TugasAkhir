@extends('layouts.adminlte')

@section('title', 'Satuan Produk')
@section('page_title', 'Kelola Satuan Produk')

@section('content')
<div class="row">
    <div class="col-12">
        <button class="btn btn-primary mb-3" onclick="tambahData()">
            <i class="fas fa-plus"></i> Tambah Satuan
        </button>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Satuan</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row->nama_satuan }}</td>
                            <td>
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
            <form id="form-satuan" method="POST">
                @csrf
                <input type="hidden" name="_method" id="method" value="POST">
                <input type="hidden" id="satuan_id">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Tambah Satuan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Satuan</label>
                        <input type="text" name="nama_satuan" id="nama_satuan" class="form-control" placeholder="Contoh: Pcs, Kg, Sak" required>
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
        $('#form-satuan')[0].reset();
        $('#method').val('POST');
        $('#modal-title').text('Tambah Satuan');
        $('#modal-form').modal('show');
    }

    function editData(id) {
        $.get('/admin/satuan/' + id + '/edit', function(data) {
            $('#modal-title').text('Edit Satuan');
            $('#method').val('PUT');
            $('#satuan_id').val(id);
            $('#nama_satuan').val(data.nama_satuan);
            $('#modal-form').modal('show');
        });
    }

    function deleteData(id) {
        if(confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            $.ajax({
                url: '/admin/satuan/' + id,
                type: 'POST',
                data: { '_method': 'DELETE' },
                success: function(response) {
                    alert(response.message);
                    location.reload();
                }
            });
        }
    }

    $('#form-satuan').on('submit', function(e) {
        e.preventDefault();
        
        let id = $('#satuan_id').val();
        let url = $('#method').val() === 'POST' ? '/admin/satuan' : '/admin/satuan/' + id;

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
                alert('Gagal menyimpan! Cek kembali isian Anda.');
            }
        });
    });
</script>
@endpush