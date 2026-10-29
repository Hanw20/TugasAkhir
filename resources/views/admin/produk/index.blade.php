@extends('layouts.adminlte')

@section('title', 'Data Produk')
@section('page_title', 'Kelola Katalog Produk')

@section('content')
<div class="row">
    <div class="col-12">
        <button class="btn btn-primary mb-3" onclick="tambahData()">
            <i class="fas fa-plus"></i> Tambah Produk
        </button>

        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Kode</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Merek</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th width="18%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row->kode_produk }}</td>
                            <td>{{ $row->nama_produk }}</td>
                            <td>{{ $row->kategori->nama_kategori ?? '-' }}</td>
                            <td>{{ $row->merek->nama_merek ?? '-' }}</td>
                            <!-- Format Uang Rupiah Otomatis -->
                            <td>Rp {{ number_format($row->harga_jual, 0, ',', '.') }}</td>
                            <td>{{ $row->stok }} {{ $row->satuanDasar->nama_satuan ?? '' }}</td>
                            <td>
                                <!-- Tombol Detail untuk memanggil fungsi show() nanti -->
                                <a href="/admin/produk/{{ $row->id }}" class="btn btn-sm btn-info">Detail</a>
                                
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
    <!-- modal-lg agar pop-up nya lebih lebar -->
    <div class="modal-dialog modal-lg"> 
        <div class="modal-content">
            <form id="form-produk" method="POST">
                @csrf
                <input type="hidden" name="_method" id="method" value="POST">
                <input type="hidden" id="produk_id">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Tambah Produk</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Kode Produk</label>
                            <input type="text" name="kode_produk" id="kode_produk" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Nama Produk</label>
                            <input type="text" name="nama_produk" id="nama_produk" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Kategori</label>
                            <select name="kategori_produk_id" id="kategori_produk_id" class="form-control" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategori as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Merek</label>
                            <select name="merek_id" id="merek_id" class="form-control" required>
                                <option value="">-- Pilih Merek --</option>
                                @foreach($merek as $m)
                                    <option value="{{ $m->id }}">{{ $m->nama_merek }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Satuan</label>
                            <select name="satuan_dasar_id" id="satuan_dasar_id" class="form-control" required>
                                <option value="">-- Pilih Satuan --</option>
                                @foreach($satuan as $s)
                                    <option value="{{ $s->id }}">{{ $s->nama_satuan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Harga Beli (Rp)</label>
                            <input type="number" name="harga_beli" id="harga_beli" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Harga Jual (Rp)</label>
                            <input type="number" name="harga_jual" id="harga_jual" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Stok Awal</label>
                            <input type="number" name="stok" id="stok" class="form-control" value="0" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Batas Stok Minimum</label>
                            <input type="number" name="stok_minimum" id="stok_minimum" class="form-control" value="5" required>
                        </div>
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
        $('#form-produk')[0].reset();
        $('#method').val('POST');
        $('#modal-title').text('Tambah Produk');
        $('#modal-form').modal('show');
    }

    function editData(id) {
        $.get('/admin/produk/' + id + '/edit', function(data) {
            $('#modal-title').text('Edit Produk');
            $('#method').val('PUT');
            $('#produk_id').val(id);
            
            // Isi semua inputan
            $('#kode_produk').val(data.kode_produk);
            $('#nama_produk').val(data.nama_produk);
            $('#kategori_produk_id').val(data.kategori_produk_id);
            $('#merek_id').val(data.merek_id);
            $('#satuan_dasar_id').val(data.satuan_dasar_id);
            
            // Math.floor menghilangkan angka desimal bawaan database
            $('#harga_beli').val(Math.floor(data.harga_beli));
            $('#harga_jual').val(Math.floor(data.harga_jual));
            $('#stok').val(Math.floor(data.stok));
            $('#stok_minimum').val(Math.floor(data.stok_minimum));
            
            $('#modal-form').modal('show');
        });
    }

    function deleteData(id) {
        if(confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
            $.ajax({
                url: '/admin/produk/' + id,
                type: 'POST',
                data: { '_method': 'DELETE' },
                success: function(response) {
                    alert(response.message);
                    location.reload();
                }
            });
        }
    }

    $('#form-produk').on('submit', function(e) {
        e.preventDefault();
        let id = $('#produk_id').val();
        let url = $('#method').val() === 'POST' ? '/admin/produk' : '/admin/produk/' + id;

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
                alert('Gagal menyimpan! Pastikan kode belum dipakai dan semua data wajib diisi.');
            }
        });
    });
</script>
@endpush