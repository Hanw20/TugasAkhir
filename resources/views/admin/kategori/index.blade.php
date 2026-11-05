@extends('layouts.adminlte')

@section('title', 'Kategori Produk')
@section('page_title', 'Kelola Kategori Produk')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Tombol Tambah memanggil fungsi tambahData() di Javascript -->
        <button class="btn btn-primary mb-3" onclick="tambahData()">
            <i class="fas fa-plus"></i> Tambah Kategori
        </button>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Kategori</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row->nama_kategori }}</td>
                            <td>
                                <a href="/admin/kategori/{{ $row->id }}" class="btn btn-sm btn-info">Detail</a>
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

<!-- ================= BOOTSTRAP 5 MODAL KATEGORI ================= -->
<div class="modal fade" id="modal-form" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0">
            <form id="form-kategori" method="POST">
                @csrf
                <input type="hidden" name="_method" id="method" value="POST">
                <input type="hidden" id="kategori_id">
                
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fs-5 fw-bold" id="modal-title">
                        <i class="bi bi-tag me-2"></i> Tambah Kategori
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-0">
                        <label for="nama_kategori" class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kategori" id="nama_kategori" class="form-control" placeholder="Contoh: Semen, Besi, Cat, Keramik" required>
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
    // 1. Setup CSRF token keamanan Laravel untuk AJAX
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // 2. Fungsi Tampilkan Modal Tambah
    function tambahData() {
        $('#form-kategori')[0].reset();
        $('#method').val('POST'); // Method diset ke POST
        $('#modal-title').text('Tambah Kategori');
        $('#modal-form').modal('show');
    }

    // 3. Fungsi Tampilkan Modal Edit & Ambil Data Lama
    function editData(id) {
        $.get('/admin/kategori/' + id + '/edit', function(data) {
            $('#modal-title').text('Edit Kategori');
            $('#method').val('PUT'); // Method diset ke PUT untuk update
            $('#kategori_id').val(id);
            $('#nama_kategori').val(data.nama_kategori);
            $('#modal-form').modal('show');
        });
    }

    // 4. Fungsi Hapus Data
    function deleteData(id) {
        if(confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            $.ajax({
                url: '/admin/kategori/' + id,
                type: 'POST',
                data: { '_method': 'DELETE' },
                success: function(response) {
                    alert(response.message);
                    location.reload(); // Refresh halaman setelah sukses
                }
            });
        }
    }

    // 5. Fungsi Submit Form (Menyimpan Tambah/Edit) tanpa Pindah Halaman
    $('#form-kategori').on('submit', function(e) {
        e.preventDefault(); // Mencegah form loading pindah halaman
        
        let id = $('#kategori_id').val();
        let url = $('#method').val() === 'POST' ? '/admin/kategori' : '/admin/kategori/' + id;

        $.ajax({
            url: url,
            type: 'POST',
            data: $(this).serialize(), // Mengambil semua isian form
            success: function(response) {
                $('#modal-form').modal('hide'); // Tutup pop-up
                alert(response.message);
                location.reload(); // Refresh tabel
            },
            error: function(err) {
                alert('Gagal menyimpan! Cek nama kategori tidak boleh kosong.');
            }
        });
    });
</script>
@endpush