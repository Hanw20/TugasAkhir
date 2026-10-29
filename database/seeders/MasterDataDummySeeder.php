<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SatuanProduk;
use App\Models\KategoriProduk;
use App\Models\MerekProduk;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Produk;
use App\Models\MasterKonversi;

class MasterDataDummySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Data Satuan
        $satuanSak = SatuanProduk::firstOrCreate(['nama_satuan' => 'Sak']);
        $satuanKg  = SatuanProduk::firstOrCreate(['nama_satuan' => 'Kg']);
        $satuanDus = SatuanProduk::firstOrCreate(['nama_satuan' => 'Dus']);
        $satuanPcs = SatuanProduk::firstOrCreate(['nama_satuan' => 'Pcs']);
        $satuanBtg = SatuanProduk::firstOrCreate(['nama_satuan' => 'Batang']);
        $satuanMtr = SatuanProduk::firstOrCreate(['nama_satuan' => 'Meter']);
        $satuanKlg = SatuanProduk::firstOrCreate(['nama_satuan' => 'Kaleng']);

        // 2. Data Kategori
        $katSemen = KategoriProduk::firstOrCreate(['nama_kategori' => 'Semen & Pasir']);
        $katCat   = KategoriProduk::firstOrCreate(['nama_kategori' => 'Cat & Pelapis']);
        $katBesi  = KategoriProduk::firstOrCreate(['nama_kategori' => 'Besi & Baja']);
        $katPipa  = KategoriProduk::firstOrCreate(['nama_kategori' => 'Pipa & Sanitasi']);
        $katKeramik = KategoriProduk::firstOrCreate(['nama_kategori' => 'Keramik & Granit']);

        // 3. Data Merek
        $merekTigaRoda = MerekProduk::firstOrCreate(['nama_merek' => 'Tiga Roda']);
        $merekHolcim   = MerekProduk::firstOrCreate(['nama_merek' => 'Dynamix / Holcim']);
        $merekDulux    = MerekProduk::firstOrCreate(['nama_merek' => 'Dulux']);
        $merekAvian    = MerekProduk::firstOrCreate(['nama_merek' => 'Avian']);
        $merekRucika   = MerekProduk::firstOrCreate(['nama_merek' => 'Rucika']);
        $merekRoman    = MerekProduk::firstOrCreate(['nama_merek' => 'Roman']);

        // 4. Data Supplier
        $sup1 = Supplier::firstOrCreate(
            ['kode_supplier' => 'SUP-001'],
            [
                'nama_supplier' => 'PT Indocement Tunggal Prakarsa',
                'alamat'        => 'Jl. Mayor Oking Jaya Atmaja No. 1, Bogor',
                'nomor_telepon' => '021-8754321',
                'email'         => 'sales@indocement.co.id'
            ]
        );

        $sup2 = Supplier::firstOrCreate(
            ['kode_supplier' => 'SUP-002'],
            [
                'nama_supplier' => 'PT Avia Avian Tbk',
                'alamat'        => 'Jl. Raya Buduran, Sidoarjo',
                'nomor_telepon' => '031-8921234',
                'email'         => 'distribusi@avianbrands.com'
            ]
        );

        $sup3 = Supplier::firstOrCreate(
            ['kode_supplier' => 'SUP-003'],
            [
                'nama_supplier' => 'PT Wahana Duta Persada (Rucika)',
                'alamat'        => 'Kawasan Industri MM2100, Cikarang',
                'nomor_telepon' => '021-8980001',
                'email'         => 'order@rucika.co.id'
            ]
        );

        // 5. Data Customer
        Customer::firstOrCreate(
            ['nama_customer' => 'Bpk. Budi Santoso (Kontraktor CV Mandiri)'],
            [
                'no_telepon' => '081234567890',
                'alamat'     => 'Perum Permata Indah Blok A No. 12'
            ]
        );

        Customer::firstOrCreate(
            ['nama_customer' => 'Ibu Siti Rahmawati'],
            [
                'no_telepon' => '085712345678',
                'alamat'     => 'Jl. Flamboyan No. 45'
            ]
        );

        Customer::firstOrCreate(
            ['nama_customer' => 'Pak Joko (Tukang Bangunan)'],
            [
                'no_telepon' => '087890123456',
                'alamat'     => 'Kp. Sukamaju RT 03/RW 02'
            ]
        );

        // 6. Data Produk
        Produk::firstOrCreate(
            ['kode_produk' => 'PRD-SMN-001'],
            [
                'nama_produk'        => 'Semen Tiga Roda Portland Composite 50kg',
                'kategori_produk_id' => $katSemen->id,
                'merek_id'           => $merekTigaRoda->id,
                'satuan_dasar_id'    => $satuanSak->id,
                'harga_beli'         => 65000,
                'harga_jual'         => 72000,
                'stok'               => 120,
                'stok_minimum'       => 20
            ]
        );

        Produk::firstOrCreate(
            ['kode_produk' => 'PRD-SMN-002'],
            [
                'nama_produk'        => 'Semen Dynamix Serbaguna 40kg',
                'kategori_produk_id' => $katSemen->id,
                'merek_id'           => $merekHolcim->id,
                'satuan_dasar_id'    => $satuanSak->id,
                'harga_beli'         => 53000,
                'harga_jual'         => 59000,
                'stok'               => 15,
                'stok_minimum'       => 20 // Status: Stok Menipis!
            ]
        );

        Produk::firstOrCreate(
            ['kode_produk' => 'PRD-CAT-001'],
            [
                'nama_produk'        => 'Cat Tembok Dulux Weathershield Putih 2.5L',
                'kategori_produk_id' => $katCat->id,
                'merek_id'           => $merekDulux->id,
                'satuan_dasar_id'    => $satuanKlg->id,
                'harga_beli'         => 240000,
                'harga_jual'         => 275000,
                'stok'               => 30,
                'stok_minimum'       => 5
            ]
        );

        Produk::firstOrCreate(
            ['kode_produk' => 'PRD-CAT-002'],
            [
                'nama_produk'        => 'Cat Kayu & Besi Avian Hitam 1kg',
                'kategori_produk_id' => $katCat->id,
                'merek_id'           => $merekAvian->id,
                'satuan_dasar_id'    => $satuanKlg->id,
                'harga_beli'         => 62000,
                'harga_jual'         => 70000,
                'stok'               => 50,
                'stok_minimum'       => 10
            ]
        );

        Produk::firstOrCreate(
            ['kode_produk' => 'PRD-PPA-001'],
            [
                'nama_produk'        => 'Pipa PVC Rucika AW 1/2 Inch (4 Meter)',
                'kategori_produk_id' => $katPipa->id,
                'merek_id'           => $merekRucika->id,
                'satuan_dasar_id'    => $satuanBtg->id,
                'harga_beli'         => 28000,
                'harga_jual'         => 35000,
                'stok'               => 80,
                'stok_minimum'       => 15
            ]
        );

        Produk::firstOrCreate(
            ['kode_produk' => 'PRD-KRM-001'],
            [
                'nama_produk'        => 'Keramik Lantai Roman 40x40 Putih Polos',
                'kategori_produk_id' => $katKeramik->id,
                'merek_id'           => $merekRoman->id,
                'satuan_dasar_id'    => $satuanDus->id,
                'harga_beli'         => 75000,
                'harga_jual'         => 88000,
                'stok'               => 45,
                'stok_minimum'       => 10
            ]
        );

        // 7. Master Rumus Konversi
        MasterKonversi::firstOrCreate(
            [
                'satuan_asal_id'   => $satuanDus->id,
                'satuan_tujuan_id' => $satuanPcs->id,
            ],
            ['nilai_konversi' => 12]
        );

        MasterKonversi::firstOrCreate(
            [
                'satuan_asal_id'   => $satuanSak->id,
                'satuan_tujuan_id' => $satuanKg->id,
            ],
            ['nilai_konversi' => 50]
        );
    }
}