@extends('layouts.adminlte')

@section('title', 'Kategori Produk')
@section('page_title', 'Kelola Kategori Produk')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Tombol Tambah memanggil fungsi tambahData() di Javascript -->
        <button class="btn mb-3 text-white shadow-sm" style="background-color: #2A9D8F; border-radius: 8px; font-weight: 500; padding: 7px 16px;" onclick="tambahData()">
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
                                    <a href="/admin/kategori/{{ $row->id }}" class="btn btn-sm" style="background-color: #E0F2FE; color: #0369A1; border-radius: 6px; font-weight: 500; font-size: 0.8rem; padding: 4px 10px;">Detail</a>
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

<!-- ================= BOOTSTRAP 5 MODAL KATEGORI ================= -->
<div class="modal fade" id="modal-form" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0" style="border-radius: 12px; overflow: hidden;">
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
                        <label for="nama_kategori" class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kategori" id="nama_kategori" class="form-control" placeholder="Contoh: Semen, Besi, Cat, Keramik" required>
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