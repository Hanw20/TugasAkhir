@extends('layouts.adminlte')

@section('title', 'Data Supplier')
@section('page_title', 'Kelola Data Supplier')

@section('content')
<div class="row">
    <div class="col-12">
        <button class="btn btn-primary mb-3" onclick="tambahData()">
            <i class="fas fa-plus"></i> Tambah Supplier
        </button>

        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Kode</th>
                            <th>Nama Supplier</th>
                            <th>No. Telepon</th>
                            <th>Alamat</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row->kode_supplier }}</td>
                            <td>{{ $row->nama_supplier }}</td>
                            <td>{{ $row->nomor_telepon }}</td>
                            <td>{{ $row->alamat }}</td>
                            <td>
                                <a href="/admin/supplier/{{ $row->id }}" class="btn btn-sm btn-info">Detail</a>
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
            <form id="form-supplier" method="POST">
                @csrf
                <input type="hidden" name="_method" id="method" value="POST">
                <input type="hidden" id="supplier_id">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Tambah Supplier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Supplier</label>
                        <input type="text" name="kode_supplier" id="kode_supplier" class="form-control" placeholder="Contoh: SUP-001" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Supplier</label>
                        <input type="text" name="nama_supplier" id="nama_supplier" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Nomor Telepon</label>
                        <input type="text" name="nomor_telepon" id="nomor_telepon" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="alamat" id="alamat" class="form-control" rows="3"></textarea>
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
        $('#form-supplier')[0].reset();
        $('#method').val('POST');
        $('#modal-title').text('Tambah Supplier');
        $('#modal-form').modal('show');
    }

    function editData(id) {
        $.get('/admin/supplier/' + id + '/edit', function(data) {
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
        if(confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            $.ajax({
                url: '/admin/supplier/' + id,
                type: 'POST',
                data: { '_method': 'DELETE' },
                success: function(response) {
                    alert(response.message);
                    location.reload();
                }
            });
        }
    }

    $('#form-supplier').on('submit', function(e) {
        e.preventDefault();
        
        let id = $('#supplier_id').val();
        let url = $('#method').val() === 'POST' ? '/admin/supplier' : '/admin/supplier/' + id;

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
                // Laravel akan mengirim error otomatis jika Kode Supplier kembar
                alert('Gagal menyimpan! Pastikan Kode Supplier belum dipakai dan form terisi dengan benar.');
            }
        });
    });
</script>
@endpush