<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// ========================================================
// RUTE KHUSUS ADMIN (Dilindungi Middleware 'role:admin')
// ========================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

    // --- MASTER DATA ---
    Route::resource('kategori', App\Http\Controllers\KategoriProdukController::class);
    Route::resource('merek', App\Http\Controllers\MerekProdukController::class);
    Route::resource('satuan', App\Http\Controllers\SatuanProdukController::class);
    Route::resource('supplier', App\Http\Controllers\SupplierController::class);
    Route::post('supplier/{id}/nilai', [App\Http\Controllers\SupplierController::class, 'storePenilaian'])->name('supplier.nilai.store');
    Route::post('supplier/{id}/produk', [App\Http\Controllers\SupplierController::class, 'attachProduk'])->name('supplier.produk.attach');
    Route::delete('supplier/{id}/produk/{produk_id}', [App\Http\Controllers\SupplierController::class, 'detachProduk'])->name('supplier.produk.detach');
    Route::resource('customer', App\Http\Controllers\CustomerController::class);
    Route::resource('produk', App\Http\Controllers\ProdukController::class);
    Route::resource('konversi', App\Http\Controllers\KonversiController::class);

    // --- MODUL PEMBELIAN ---
    Route::resource('pembelian', App\Http\Controllers\PembelianController::class);
    // Penerimaan Barang Datang
    // Alur Penerimaan Barang Datang
    Route::get('pembelian/{id}/terima', [App\Http\Controllers\PenerimaanPembelianController::class, 'create'])->name('pembelian.terima.create');
    Route::post('pembelian/{id}/terima', [App\Http\Controllers\PenerimaanPembelianController::class, 'store'])->name('pembelian.terima.store');
    Route::get('pembelian-penerimaan', [App\Http\Controllers\PenerimaanPembelianController::class, 'index'])->name('pembelian.penerimaan.index');
    // Rute Baru: Lihat Detail Bukti Penerimaan Fisik
    Route::get('pembelian-penerimaan/{id}', [App\Http\Controllers\PenerimaanPembelianController::class, 'show'])->name('pembelian.penerimaan.show');

    // Pembayaran Hutang Usaha (Tempo)
    Route::get('pembayaran-hutang', [App\Http\Controllers\PembayaranHutangController::class, 'index'])->name('pembayaran-hutang.index');
    Route::post('pembayaran-hutang', [App\Http\Controllers\PembayaranHutangController::class, 'store'])->name('pembayaran-hutang.store');


    // ================= PENJUALAN =================
    Route::resource('penjualan', App\Http\Controllers\PenjualanController::class);

    // Alur Pengiriman Barang ke Customer (Surat Jalan Gudang)
    Route::get('penjualan/{id}/kirim', [App\Http\Controllers\PengirimanPenjualanController::class, 'create'])->name('penjualan.kirim.create');
    Route::post('penjualan/{id}/kirim', [App\Http\Controllers\PengirimanPenjualanController::class, 'store'])->name('penjualan.kirim.store');
    Route::get('penjualan-pengiriman', [App\Http\Controllers\PengirimanPenjualanController::class, 'index'])->name('penjualan.pengiriman.index');
    Route::get('penjualan-pengiriman/{id}', [App\Http\Controllers\PengirimanPenjualanController::class, 'show'])->name('penjualan.pengiriman.show');

    // Alur Pelunasan Piutang Customer (Tempo/Bon)
    Route::get('piutang-customer', [App\Http\Controllers\PembayaranPiutangController::class, 'index'])->name('piutang-customer.index');
    Route::post('piutang-customer', [App\Http\Controllers\PembayaranPiutangController::class, 'store'])->name('piutang-customer.store');
    // Modul Stok Opname
    Route::resource('stok-opname', App\Http\Controllers\StokOpnameController::class);

    // Modul Konversi Produk (Transaksi Repacking Stok)
    Route::resource('konversi-produk', App\Http\Controllers\KonversiProdukController::class);

    // Sesi Perhitungan AHP (Perbandingan Berpasangan Kriteria)
    Route::get('sesi-perhitungan-ahp', [App\Http\Controllers\SesiPerhitunganAhpController::class, 'index'])->name('sesi-perhitungan-ahp.index');
    Route::post('sesi-perhitungan-ahp', [App\Http\Controllers\SesiPerhitunganAhpController::class, 'store'])->name('sesi-perhitungan-ahp.store');
    Route::delete('sesi-perhitungan-ahp/{id}', [App\Http\Controllers\SesiPerhitunganAhpController::class, 'destroy'])->name('sesi-perhitungan-ahp.destroy');
    // Rekomendasi Supplier (Metode SAW per Produk)
    Route::get('rekomendasi-supplier', [App\Http\Controllers\RekomendasiSupplierController::class, 'index'])->name('rekomendasi-supplier.index');
});

// ========================================================
// RUTE KHUSUS KASIR (Dilindungi Middleware 'role:kasir')
// ========================================================
Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
});