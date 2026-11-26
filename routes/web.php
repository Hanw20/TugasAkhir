<?php

use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', function () {
    return view('auth/login');
});
Route::get('/home', [App\Http\Controllers\DashboardController::class, 'index'])->name('home');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

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

    Route::resource('pembelian', App\Http\Controllers\PembelianController::class);
    Route::get('pembelian/{id}/terima', [App\Http\Controllers\PenerimaanPembelianController::class, 'create'])->name('pembelian.terima.create');
    Route::post('pembelian/{id}/terima', [App\Http\Controllers\PenerimaanPembelianController::class, 'store'])->name('pembelian.terima.store');
    Route::get('pembelian-penerimaan', [App\Http\Controllers\PenerimaanPembelianController::class, 'index'])->name('pembelian.penerimaan.index');
    Route::get('pembelian-penerimaan/{id}', [App\Http\Controllers\PenerimaanPembelianController::class, 'show'])->name('pembelian.penerimaan.show');

    Route::get('pembayaran-hutang', [App\Http\Controllers\PembayaranHutangController::class, 'index'])->name('pembayaran-hutang.index');
    Route::post('pembayaran-hutang', [App\Http\Controllers\PembayaranHutangController::class, 'store'])->name('pembayaran-hutang.store');

    Route::resource('penjualan', App\Http\Controllers\PenjualanController::class);

    Route::get('penjualan/{id}/kirim', [App\Http\Controllers\PengirimanPenjualanController::class, 'create'])->name('penjualan.kirim.create');
    Route::post('penjualan/{id}/kirim', [App\Http\Controllers\PengirimanPenjualanController::class, 'store'])->name('penjualan.kirim.store');
    Route::get('penjualan-pengiriman', [App\Http\Controllers\PengirimanPenjualanController::class, 'index'])->name('penjualan.pengiriman.index');
    Route::get('penjualan-pengiriman/{id}', [App\Http\Controllers\PengirimanPenjualanController::class, 'show'])->name('penjualan.pengiriman.show');

    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('pembelian', [App\Http\Controllers\PembelianController::class, 'laporan'])->name('pembelian');
        Route::get('penjualan', [App\Http\Controllers\PenjualanController::class, 'laporan'])->name('penjualan');
        Route::get('penilaian', [App\Http\Controllers\SupplierController::class, 'laporanPenilaian'])->name('penilaian');
    });

    Route::get('piutang-customer', [App\Http\Controllers\PembayaranPiutangController::class, 'index'])->name('piutang-customer.index');
    Route::post('piutang-customer', [App\Http\Controllers\PembayaranPiutangController::class, 'store'])->name('piutang-customer.store');
    Route::resource('stok-opname', App\Http\Controllers\StokOpnameController::class);
    Route::post('stok-opname/{id}/reconcile', [App\Http\Controllers\StokOpnameController::class, 'reconcile'])->name('stok-opname.reconcile');

    Route::resource('konversi-produk', App\Http\Controllers\KonversiProdukController::class);

    Route::get('sesi-perhitungan-ahp', [App\Http\Controllers\SesiPerhitunganAhpController::class, 'index'])->name('sesi-perhitungan-ahp.index');
    Route::post('sesi-perhitungan-ahp', [App\Http\Controllers\SesiPerhitunganAhpController::class, 'store'])->name('sesi-perhitungan-ahp.store');
    Route::delete('sesi-perhitungan-ahp/{id}', [App\Http\Controllers\SesiPerhitunganAhpController::class, 'destroy'])->name('sesi-perhitungan-ahp.destroy');
    Route::get('rekomendasi-supplier', [App\Http\Controllers\RekomendasiSupplierController::class, 'index'])->name('rekomendasi-supplier.index');
    Route::get('rekomendasi-supplier/{id}', [App\Http\Controllers\RekomendasiSupplierController::class, 'show'])->name('rekomendasi-supplier.show');
});

Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
});
