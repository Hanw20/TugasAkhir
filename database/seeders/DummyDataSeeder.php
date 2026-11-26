<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Truncate tabel yang akan diisi dummy
        $tables = [
            'pembayaran_piutang', 'detail_pengiriman_penjualan', 'pengiriman_penjualan',
            'detail_penjualan', 'penjualan', 'pembayaran_hutang', 'riwayat_penilaian_supplier',
            'detail_penerimaan_pembelian', 'penerimaan_pembelian', 'detail_pembelian', 'pembelian',
            'detail_stok_opname', 'stok_opname', 'konversi', 'saw_normalisasi', 'produk_supplier',
            'master_konversi', 'produk', 'customers', 'suppliers', 'merek_produk',
            'kategori_produk', 'satuan_produk', 'perbandingan_kriteria_ahp',
            'sesi_perhitungan_ahp', 'kriteria_supplier', 'users'
        ];

        foreach ($tables as $t) {
            DB::table($t)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = Carbon::now();

        // ── 1. USERS ──────────────────────────────────────────────────────────
        $users = [
            ['id' => 1, 'nama' => 'Pemilik Toko (Admin)', 'email' => 'admin@tbb.com', 'password' => Hash::make('admin123'), 'role' => 'admin', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'nama' => 'Budi Santoso (Kasir Shift 1)', 'email' => 'kasir1@tbb.com', 'password' => Hash::make('kasir123'), 'role' => 'kasir', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'nama' => 'Siti Aminah (Kasir Shift 2)', 'email' => 'kasir2@tbb.com', 'password' => Hash::make('kasir123'), 'role' => 'kasir', 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('users')->insert($users);

        // ── 2. SATUAN PRODUK ──────────────────────────────────────────────────
        $satuan = [
            ['id' => 1, 'nama_satuan' => 'Sak', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'nama_satuan' => 'Kg', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'nama_satuan' => 'Batang', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'nama_satuan' => 'Meter', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'nama_satuan' => 'Dus', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'nama_satuan' => 'Pcs', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'nama_satuan' => 'Pail (20L)', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8, 'nama_satuan' => 'Kaleng (5L)', 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('satuan_produk')->insert($satuan);

        // ── 3. KATEGORI PRODUK ────────────────────────────────────────────────
        $kategori = [
            ['id' => 1, 'nama_kategori' => 'Semen & Pasir', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'nama_kategori' => 'Besi & Logam', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'nama_kategori' => 'Pipa & Plumbing', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'nama_kategori' => 'Cat & Pelapis', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'nama_kategori' => 'Keramik & Granit', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'nama_kategori' => 'Paku, Baut & Perkakas', 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('kategori_produk')->insert($kategori);

        // ── 4. MEREK PRODUK ───────────────────────────────────────────────────
        $merek = [
            ['id' => 1, 'nama_merek' => 'Semen Gresik', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'nama_merek' => 'Semen Tiga Roda', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'nama_merek' => 'Krakatau Steel (KS)', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'nama_merek' => 'Rucika', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'nama_merek' => 'Avian Brands', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'nama_merek' => 'Dulux', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'nama_merek' => 'Mulia Ceramics', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8, 'nama_merek' => 'Lokal / Non-Brand', 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('merek_produk')->insert($merek);

        // ── 5. SUPPLIERS (5 Supplier Variatif) ────────────────────────────────
        $suppliers = [
            ['id' => 1, 'kode_supplier' => 'SUP-001', 'nama_supplier' => 'PT Semen Perkasa Nusantara', 'alamat' => 'Jl. Industri Gresik No. 88, Gresik', 'nomor_telepon' => '08123456701', 'email' => 'sales@semenperkasa.co.id', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'kode_supplier' => 'SUP-002', 'nama_supplier' => 'CV Baja Sakti Sejahtera', 'alamat' => 'Kawasan Industri Rungkut Kav. 12, Surabaya', 'nomor_telepon' => '08123456702', 'email' => 'bajasakti@gmail.com', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'kode_supplier' => 'SUP-003', 'nama_supplier' => 'Distributor Mega Bangunan Jaya', 'alamat' => 'Jl. Kenjeran No. 204, Surabaya', 'nomor_telepon' => '08123456703', 'email' => 'megabangunan@yahoo.com', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'kode_supplier' => 'SUP-004', 'nama_supplier' => 'UD Sumber Abadi Cat & Pipa', 'alamat' => 'Jl. Kalianak Barat No. 55, Surabaya', 'nomor_telepon' => '08123456704', 'email' => 'sumberabadi@gmail.com', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'kode_supplier' => 'SUP-005', 'nama_supplier' => 'CV Harapan Mitra Mandiri', 'alamat' => 'Jl. Margomulyo Indah Blok C-5, Surabaya', 'nomor_telepon' => '08123456705', 'email' => 'harapanmitra@outlook.com', 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('suppliers')->insert($suppliers);

        // ── 6. CUSTOMERS (B2B Tempo & Pelanggan Harian) ──────────────────────
        $customers = [
            ['id' => 1, 'nama_customer' => 'CV Citra Karya Mandiri (Kontraktor)', 'no_telepon' => '08112233441', 'alamat' => 'Perumahan Citraland Blok BD-12, Surabaya', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'nama_customer' => 'Toko Bangunan Sumber Lancar (Sesama UMKM)', 'no_telepon' => '08112233442', 'alamat' => 'Jl. Raya Sepanjang No. 45, Sidoarjo', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'nama_customer' => 'Pak Joko Widodo (Mandor Proyek)', 'no_telepon' => '08112233443', 'alamat' => 'Proyek Ruko Dharmahusada Mas, Surabaya', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'nama_customer' => 'Ibu Hendrawan (Pelanggan Renovasi Rumah)', 'no_telepon' => '08112233444', 'alamat' => 'Jl. Manyar Kertoarjo No. 78, Surabaya', 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('customers')->insert($customers);

        // ── 7. KRITERIA SUPPLIER (6 Kriteria AHP-SAW) ─────────────────────────
        $kriteria = [
            ['id' => 1, 'kode_kriteria' => 'K1', 'nama_kriteria' => 'Harga Beli',          'jenis_kriteria' => 'cost',    'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'kode_kriteria' => 'K2', 'nama_kriteria' => 'Kualitas Produk',     'jenis_kriteria' => 'benefit', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'kode_kriteria' => 'K3', 'nama_kriteria' => 'Kecepatan Respon',   'jenis_kriteria' => 'benefit', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'kode_kriteria' => 'K4', 'nama_kriteria' => 'Garansi',     'jenis_kriteria' => 'benefit', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'kode_kriteria' => 'K5', 'nama_kriteria' => 'Waktu Pengiriman',   'jenis_kriteria' => 'cost',    'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'kode_kriteria' => 'K6', 'nama_kriteria' => 'Tempo Pembayaran',   'jenis_kriteria' => 'benefit', 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('kriteria_supplier')->insert($kriteria);

        // ── 8. MASTER KONVERSI ────────────────────────────────────────────────
        $masterKonversi = [
            ['id' => 1, 'satuan_asal_id' => 1, 'satuan_tujuan_id' => 2, 'nilai_konversi' => 50.0000, 'created_at' => $now, 'updated_at' => $now], // 1 Sak = 50 Kg
            ['id' => 2, 'satuan_asal_id' => 5, 'satuan_tujuan_id' => 6, 'nilai_konversi' => 12.0000, 'created_at' => $now, 'updated_at' => $now], // 1 Dus = 12 Pcs
            ['id' => 3, 'satuan_asal_id' => 7, 'satuan_tujuan_id' => 8, 'nilai_konversi' => 4.0000,  'created_at' => $now, 'updated_at' => $now], // 1 Pail (20L) = 4 Kaleng (5L)
        ];
        DB::table('master_konversi')->insert($masterKonversi);

        // ── 9. PRODUK (Kondisi Normal, Stok Menipis, Satuan Timbang) ──────────
        $produk = [
            // 1. Semen Kemasan Sak (Kondisi: Stok Banyak)
            [
                'id' => 1, 'kode_produk' => 'PRD-SMN-01', 'nama_produk' => 'Semen Gresik 50 Kg',
                'kategori_produk_id' => 1, 'merek_id' => 1, 'satuan_dasar_id' => 1, 'metode_jual' => 'satuan',
                'harga_beli' => 62000, 'harga_beli_terakhir' => 62500, 'harga_jual' => 68000,
                'stok' => 150.000, 'stok_minimum' => 30.000, 'stok_onorder_supplier' => 50.000, 'stok_onorder_customer' => 20.000,
                'created_at' => $now, 'updated_at' => $now
            ],
            // 2. Semen Eceran Repacking (Kondisi: Hasil Konversi)
            [
                'id' => 2, 'kode_produk' => 'PRD-SMN-02', 'nama_produk' => 'Semen Gresik Eceran (Per Kg)',
                'kategori_produk_id' => 1, 'merek_id' => 1, 'satuan_dasar_id' => 2, 'metode_jual' => 'satuan',
                'harga_beli' => 1300, 'harga_beli_terakhir' => 1300, 'harga_jual' => 2000,
                'stok' => 85.000, 'stok_minimum' => 20.000, 'stok_onorder_supplier' => 0.000, 'stok_onorder_customer' => 0.000,
                'created_at' => $now, 'updated_at' => $now
            ],
            // 3. Semen Tiga Roda (Kondisi: Stok Menipis di bawah Minimum Alert!)
            [
                'id' => 3, 'kode_produk' => 'PRD-SMN-03', 'nama_produk' => 'Semen Tiga Roda 50 Kg',
                'kategori_produk_id' => 1, 'merek_id' => 2, 'satuan_dasar_id' => 1, 'metode_jual' => 'satuan',
                'harga_beli' => 61000, 'harga_beli_terakhir' => 61500, 'harga_jual' => 67000,
                'stok' => 8.000, 'stok_minimum' => 25.000, 'stok_onorder_supplier' => 0.000, 'stok_onorder_customer' => 0.000,
                'created_at' => $now, 'updated_at' => $now
            ],
            // 4. Besi Beton 10mm (Kondisi: Stok Menipis)
            [
                'id' => 4, 'kode_produk' => 'PRD-BSI-01', 'nama_produk' => 'Besi Beton Polos 10mm SNI',
                'kategori_produk_id' => 2, 'merek_id' => 3, 'satuan_dasar_id' => 3, 'metode_jual' => 'satuan',
                'harga_beli' => 73000, 'harga_beli_terakhir' => 74000, 'harga_jual' => 82000,
                'stok' => 15.000, 'stok_minimum' => 40.000, 'stok_onorder_supplier' => 100.000, 'stok_onorder_customer' => 0.000,
                'created_at' => $now, 'updated_at' => $now
            ],
            // 5. Pipa PVC 3/4 Inch
            [
                'id' => 5, 'kode_produk' => 'PRD-PPA-01', 'nama_produk' => 'Pipa PVC AW 3/4 Inch Rucika',
                'kategori_produk_id' => 3, 'merek_id' => 4, 'satuan_dasar_id' => 3, 'metode_jual' => 'satuan',
                'harga_beli' => 34000, 'harga_beli_terakhir' => 34500, 'harga_jual' => 42000,
                'stok' => 65.000, 'stok_minimum' => 20.000, 'stok_onorder_supplier' => 0.000, 'stok_onorder_customer' => 15.000,
                'created_at' => $now, 'updated_at' => $now
            ],
            // 6. Cat Tembok Avitex 5L
            [
                'id' => 6, 'kode_produk' => 'PRD-CAT-01', 'nama_produk' => 'Cat Tembok Avitex Putih 5 Kg',
                'kategori_produk_id' => 4, 'merek_id' => 5, 'satuan_dasar_id' => 8, 'metode_jual' => 'satuan',
                'harga_beli' => 118000, 'harga_beli_terakhir' => 119000, 'harga_jual' => 135000,
                'stok' => 28.000, 'stok_minimum' => 10.000, 'stok_onorder_supplier' => 0.000, 'stok_onorder_customer' => 0.000,
                'created_at' => $now, 'updated_at' => $now
            ],
            // 7. Paku Kayu 5cm (Metode Jual: Nominal Timbang Rp 5.000 / Kg)
            [
                'id' => 7, 'kode_produk' => 'PRD-PKU-01', 'nama_produk' => 'Paku Kayu Curah 5cm (Timbang)',
                'kategori_produk_id' => 6, 'merek_id' => 8, 'satuan_dasar_id' => 2, 'metode_jual' => 'nominal_timbang',
                'harga_beli' => 14000, 'harga_beli_terakhir' => 14500, 'harga_jual' => 18000,
                'stok' => 92.450, 'stok_minimum' => 20.000, 'stok_onorder_supplier' => 0.000, 'stok_onorder_customer' => 0.000,
                'created_at' => $now, 'updated_at' => $now
            ],
            // 8. Keramik Mulia 40x40 Putih
            [
                'id' => 8, 'kode_produk' => 'PRD-KRM-01', 'nama_produk' => 'Keramik Lantai Mulia 40x40 Putih Polos',
                'kategori_produk_id' => 5, 'merek_id' => 7, 'satuan_dasar_id' => 5, 'metode_jual' => 'satuan',
                'harga_beli' => 52000, 'harga_beli_terakhir' => 53000, 'harga_jual' => 61000,
                'stok' => 45.000, 'stok_minimum' => 15.000, 'stok_onorder_supplier' => 0.000, 'stok_onorder_customer' => 0.000,
                'created_at' => $now, 'updated_at' => $now
            ],
        ];
        DB::table('produk')->insert($produk);

        // ── 10. PRODUK SUPPLIER (Pemetaan Multi-Supplier per Produk untuk AHP-SAW) ───
        $produkSupplier = [
            // Semen Gresik dipasok oleh 3 Supplier (Bisa dibandingankan AHP-SAW)
            ['id' => 1, 'produk_id' => 1, 'supplier_id' => 1, 'created_at' => $now, 'updated_at' => $now], // PT Semen Perkasa
            ['id' => 2, 'produk_id' => 1, 'supplier_id' => 3, 'created_at' => $now, 'updated_at' => $now], // Mega Bangunan
            ['id' => 3, 'produk_id' => 1, 'supplier_id' => 5, 'created_at' => $now, 'updated_at' => $now], // Harapan Mitra

            // Besi Beton dipasok 2 Supplier
            ['id' => 4, 'produk_id' => 4, 'supplier_id' => 2, 'created_at' => $now, 'updated_at' => $now], // CV Baja Sakti
            ['id' => 5, 'produk_id' => 4, 'supplier_id' => 3, 'created_at' => $now, 'updated_at' => $now], // Mega Bangunan

            // Pipa PVC
            ['id' => 6, 'produk_id' => 5, 'supplier_id' => 4, 'created_at' => $now, 'updated_at' => $now], // Sumber Abadi
            ['id' => 7, 'produk_id' => 5, 'supplier_id' => 3, 'created_at' => $now, 'updated_at' => $now], // Mega Bangunan
        ];
        DB::table('produk_supplier')->insert($produkSupplier);

        // ── 11. SESI PERHITUNGAN AHP ──────────────────────────────────────────
        $sesiAhp = [
            'id' => 1,
            'nama_sesi' => 'Sesi Default Pemilik Toko (Fokus Harga Utama)',
            'tanggal_hitung' => Carbon::now()->subDays(5),
            'user_id' => 1,
            'created_at' => Carbon::now()->subDays(5),
            'updated_at' => Carbon::now()->subDays(5),
        ];
        DB::table('sesi_perhitungan_ahp')->insert($sesiAhp);

        // ── 12. PERBANDINGAN KRITERIA AHP (6x6 Matriks Bujursangkar, CR <= 0.1) ──
        // Matriks AHP Konsisten (Fokus Harga Utama):
        // K1=Harga (0.4479), K2=Kualitas (0.2052), K3=Respon (0.0930), K4=Garansi (0.0979), K5=Waktu Kirim (0.1018), K6=Tempo (0.0541) -> CR = 0.0101
        $pairs = [
            [1, 1, 1.0],   [1, 2, 3.0],   [1, 3, 5.0],   [1, 4, 5.0],   [1, 5, 4.0],   [1, 6, 7.0],
            [2, 1, 0.333], [2, 2, 1.0],   [2, 3, 2.0],   [2, 4, 2.0],   [2, 5, 2.0],   [2, 6, 4.0],
            [3, 1, 0.2],   [3, 2, 0.5],   [3, 3, 1.0],   [3, 4, 1.0],   [3, 5, 1.0],   [3, 6, 2.0],
            [4, 1, 0.2],   [4, 2, 0.5],   [4, 3, 1.0],   [4, 4, 1.0],   [4, 5, 1.0],   [4, 6, 2.0],
            [5, 1, 0.25],  [5, 2, 0.5],   [5, 3, 1.0],   [5, 4, 1.0],   [5, 5, 1.0],   [5, 6, 2.0],
            [6, 1, 0.143], [6, 2, 0.25],  [6, 3, 0.5],   [6, 4, 0.5],   [6, 5, 0.5],   [6, 6, 1.0],
        ];

        $perbandinganData = [];
        foreach ($pairs as $p) {
            $perbandinganData[] = [
                'sesi_perhitungan_ahp_id' => 1,
                'kriteria_a_id' => $p[0],
                'kriteria_b_id' => $p[1],
                'nilai_perbandingan' => $p[2],
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ];
        }
        DB::table('perbandingan_kriteria_ahp')->insert($perbandinganData);

        // ── 13. TRANSAKSI KONVERSI REPACKING ──────────────────────────────────
        $konversi = [
            'id' => 1,
            'master_konversi_id' => 1, // 1 Sak = 50 Kg
            'produk_asal_id' => 1,     // Semen Gresik Sak
            'qty_asal_dipakai' => 2.000,
            'produk_tujuan_id' => 2,   // Semen Gresik Eceran
            'qty_hasil_aktual' => 98.500, // Ada susut debu 1.5 kg
            'tanggal' => Carbon::now()->subDays(3)->toDateString(),
            'user_id' => 1,
            'catatan' => 'Repacking 2 sak semen gresik menjadi eceran kiloan untuk penjualan eceran',
            'created_at' => Carbon::now()->subDays(3),
            'updated_at' => Carbon::now()->subDays(3),
        ];
        DB::table('konversi')->insert($konversi);

        // ── 14. PEMBELIAN (Variasi Status: Lunas, Belum Bayar/Jatuh Tempo, Proses) ──
        $pembelian = [
            // 1. Pembelian LUNAS & Diterima Penuh
            [
                'id' => 1, 'no_faktur' => 'PO-202611-001', 'supplier_id' => 1, 'user_id' => 1,
                'tanggal' => Carbon::now()->subDays(20)->toDateString(),
                'total' => 6250000.00, // 100 Sak x 62.500
                'tanggal_jatuh_tempo' => Carbon::now()->subDays(5),
                'status' => 'selesai', 'status_penerimaan' => 'terima_penuh', 'status_pembayaran' => 'lunas',
                'created_at' => Carbon::now()->subDays(20), 'updated_at' => Carbon::now()->subDays(10),
            ],
            // 2. Pembelian KREDIT TEMPO (Mendekati Jatuh Tempo, Belum Dibayar)
            [
                'id' => 2, 'no_faktur' => 'PO-202611-002', 'supplier_id' => 2, 'user_id' => 1,
                'tanggal' => Carbon::now()->subDays(15)->toDateString(),
                'total' => 7400000.00, // 100 Batang Besi x 74.000
                'tanggal_jatuh_tempo' => Carbon::now()->addDays(3), // 3 Hari lagi tempo!
                'status' => 'selesai', 'status_penerimaan' => 'terima_penuh', 'status_pembayaran' => 'belum_dibayar',
                'created_at' => Carbon::now()->subDays(15), 'updated_at' => Carbon::now()->subDays(12),
            ],
            // 3. Pembelian CICILAN SEBAGIAN & PENERIMAAN SEBAGIAN (Parsial)
            [
                'id' => 3, 'no_faktur' => 'PO-202611-003', 'supplier_id' => 3, 'user_id' => 1,
                'tanggal' => Carbon::now()->subDays(7)->toDateString(),
                'total' => 5000000.00,
                'tanggal_jatuh_tempo' => Carbon::now()->addDays(14),
                'status' => 'proses', 'status_penerimaan' => 'sebagian', 'status_pembayaran' => 'sebagian',
                'created_at' => Carbon::now()->subDays(7), 'updated_at' => Carbon::now()->subDays(2),
            ],
            // 4. Pembelian BARU DIPESAN (Belum Diterima Sama Sekali)
            [
                'id' => 4, 'no_faktur' => 'PO-202611-004', 'supplier_id' => 1, 'user_id' => 1,
                'tanggal' => Carbon::now()->subDays(1)->toDateString(),
                'total' => 3125000.00, // 50 Sak Semen
                'tanggal_jatuh_tempo' => Carbon::now()->addDays(20),
                'status' => 'proses', 'status_penerimaan' => 'belum_diterima', 'status_pembayaran' => 'belum_dibayar',
                'created_at' => Carbon::now()->subDays(1), 'updated_at' => Carbon::now()->subDays(1),
            ],
        ];
        DB::table('pembelian')->insert($pembelian);

        // ── 15. DETAIL PEMBELIAN ──────────────────────────────────────────────
        $detailPembelian = [
            // Faktur 1
            ['id' => 1, 'pembelian_id' => 1, 'produk_id' => 1, 'qty' => 100.000, 'harga_satuan' => 62500.00, 'subtotal' => 6250000.00, 'created_at' => Carbon::now()->subDays(20), 'updated_at' => Carbon::now()->subDays(20)],
            // Faktur 2
            ['id' => 2, 'pembelian_id' => 2, 'produk_id' => 4, 'qty' => 100.000, 'harga_satuan' => 74000.00, 'subtotal' => 7400000.00, 'created_at' => Carbon::now()->subDays(15), 'updated_at' => Carbon::now()->subDays(15)],
            // Faktur 3 (Pipa & Cat)
            ['id' => 3, 'pembelian_id' => 3, 'produk_id' => 5, 'qty' => 50.000, 'harga_satuan' => 34500.00, 'subtotal' => 1725000.00, 'created_at' => Carbon::now()->subDays(7), 'updated_at' => Carbon::now()->subDays(7)],
            ['id' => 4, 'pembelian_id' => 3, 'produk_id' => 6, 'qty' => 25.000, 'harga_satuan' => 119000.00, 'subtotal' => 2975000.00, 'created_at' => Carbon::now()->subDays(7), 'updated_at' => Carbon::now()->subDays(7)],
            ['id' => 5, 'pembelian_id' => 3, 'produk_id' => 7, 'qty' => 20.000, 'harga_satuan' => 15000.00, 'subtotal' => 300000.00, 'created_at' => Carbon::now()->subDays(7), 'updated_at' => Carbon::now()->subDays(7)],
            // Faktur 4
            ['id' => 6, 'pembelian_id' => 4, 'produk_id' => 1, 'qty' => 50.000, 'harga_satuan' => 62500.00, 'subtotal' => 3125000.00, 'created_at' => Carbon::now()->subDays(1), 'updated_at' => Carbon::now()->subDays(1)],
        ];
        DB::table('detail_pembelian')->insert($detailPembelian);

        // ── 16. PENERIMAAN PEMBELIAN ──────────────────────────────────────────
        $penerimaan = [
            ['id' => 1, 'pembelian_id' => 1, 'tanggal_terima' => Carbon::now()->subDays(18), 'user_id' => 1, 'catatan' => 'Semen diterima dalam kondisi kering dan kemasan utuh', 'created_at' => Carbon::now()->subDays(18), 'updated_at' => Carbon::now()->subDays(18)],
            ['id' => 2, 'pembelian_id' => 2, 'tanggal_terima' => Carbon::now()->subDays(13), 'user_id' => 1, 'catatan' => 'Besi beton 10mm SNI diterima sesuai spesifikasi faktur', 'created_at' => Carbon::now()->subDays(13), 'updated_at' => Carbon::now()->subDays(13)],
            ['id' => 3, 'pembelian_id' => 3, 'tanggal_terima' => Carbon::now()->subDays(4), 'user_id' => 1, 'catatan' => 'Penerimaan tahap 1: Pipa PVC dan Paku datang duluan', 'created_at' => Carbon::now()->subDays(4), 'updated_at' => Carbon::now()->subDays(4)],
        ];
        DB::table('penerimaan_pembelian')->insert($penerimaan);

        // ── 17. DETAIL PENERIMAAN PEMBELIAN ───────────────────────────────────
        $detailPenerimaan = [
            ['id' => 1, 'penerimaan_pembelian_id' => 1, 'detail_pembelian_id' => 1, 'qty_diterima' => 100.000, 'created_at' => Carbon::now()->subDays(18), 'updated_at' => Carbon::now()->subDays(18)],
            ['id' => 2, 'penerimaan_pembelian_id' => 2, 'detail_pembelian_id' => 2, 'qty_diterima' => 100.000, 'created_at' => Carbon::now()->subDays(13), 'updated_at' => Carbon::now()->subDays(13)],
            ['id' => 3, 'penerimaan_pembelian_id' => 3, 'detail_pembelian_id' => 3, 'qty_diterima' => 50.000,  'created_at' => Carbon::now()->subDays(4), 'updated_at' => Carbon::now()->subDays(4)],
            ['id' => 4, 'penerimaan_pembelian_id' => 3, 'detail_pembelian_id' => 5, 'qty_diterima' => 20.000,  'created_at' => Carbon::now()->subDays(4), 'updated_at' => Carbon::now()->subDays(4)],
        ];
        DB::table('detail_penerimaan_pembelian')->insert($detailPenerimaan);

        // ── 18. RIWAYAT PENILAIAN SUPPLIER (Evaluasi Fisik Kualitas & Evaluasi Respon/Garansi) ──
        $penilaian = [
            // Penilaian K2 (Kualitas) dari Penerimaan Barang
            ['id' => 1, 'supplier_id' => 1, 'detail_penerimaan_pembelian_id' => 1, 'kriteria_supplier_id' => 2, 'nilai' => 5.0, 'tanggal_penilaian' => Carbon::now()->subDays(18)->toDateString(), 'keterangan' => 'Kualitas semen sangat bagus, tidak ada yang beku', 'created_at' => Carbon::now()->subDays(18), 'updated_at' => Carbon::now()->subDays(18)],
            ['id' => 2, 'supplier_id' => 2, 'detail_penerimaan_pembelian_id' => 2, 'kriteria_supplier_id' => 2, 'nilai' => 4.0, 'tanggal_penilaian' => Carbon::now()->subDays(13)->toDateString(), 'keterangan' => 'Besi bagus dan lurus, sertifikat SNI terlampir', 'created_at' => Carbon::now()->subDays(13), 'updated_at' => Carbon::now()->subDays(13)],
            ['id' => 3, 'supplier_id' => 3, 'detail_penerimaan_pembelian_id' => 3, 'kriteria_supplier_id' => 2, 'nilai' => 4.0, 'tanggal_penilaian' => Carbon::now()->subDays(4)->toDateString(), 'keterangan' => 'Kualitas pipa rucika asli tanpa cacat', 'created_at' => Carbon::now()->subDays(4), 'updated_at' => Carbon::now()->subDays(4)],

            // Evaluasi K3 (Kecepatan Respon) & K4 (Garansi) Berkala Supplier di Menu Evaluasi
            ['id' => 4, 'supplier_id' => 1, 'detail_penerimaan_pembelian_id' => null, 'kriteria_supplier_id' => 3, 'nilai' => 5.0, 'tanggal_penilaian' => Carbon::now()->subDays(10)->toDateString(), 'keterangan' => 'Evaluasi Kecepatan Respon: CS sangat cepat tanggap via WhatsApp', 'created_at' => Carbon::now()->subDays(10), 'updated_at' => Carbon::now()->subDays(10)],
            ['id' => 5, 'supplier_id' => 1, 'detail_penerimaan_pembelian_id' => null, 'kriteria_supplier_id' => 4, 'nilai' => 4.0, 'tanggal_penilaian' => Carbon::now()->subDays(10)->toDateString(), 'keterangan' => 'Evaluasi Kebijakan Garansi: Sak pecah saat perjalanan diganti baru', 'created_at' => Carbon::now()->subDays(10), 'updated_at' => Carbon::now()->subDays(10)],

            ['id' => 6, 'supplier_id' => 2, 'detail_penerimaan_pembelian_id' => null, 'kriteria_supplier_id' => 3, 'nilai' => 4.0, 'tanggal_penilaian' => Carbon::now()->subDays(10)->toDateString(), 'keterangan' => 'Evaluasi Kecepatan Respon: Respon sales ramah dan jelas', 'created_at' => Carbon::now()->subDays(10), 'updated_at' => Carbon::now()->subDays(10)],
            ['id' => 7, 'supplier_id' => 2, 'detail_penerimaan_pembelian_id' => null, 'kriteria_supplier_id' => 4, 'nilai' => 4.0, 'tanggal_penilaian' => Carbon::now()->subDays(10)->toDateString(), 'keterangan' => 'Evaluasi Kebijakan Garansi: Penggantian barang cacat fleksibel', 'created_at' => Carbon::now()->subDays(10), 'updated_at' => Carbon::now()->subDays(10)],

            ['id' => 8, 'supplier_id' => 3, 'detail_penerimaan_pembelian_id' => null, 'kriteria_supplier_id' => 3, 'nilai' => 3.0, 'tanggal_penilaian' => Carbon::now()->subDays(10)->toDateString(), 'keterangan' => 'Evaluasi Kecepatan Respon: Agak lambat membalas pesan saat sore hari', 'created_at' => Carbon::now()->subDays(10), 'updated_at' => Carbon::now()->subDays(10)],
            ['id' => 9, 'supplier_id' => 3, 'detail_penerimaan_pembelian_id' => null, 'kriteria_supplier_id' => 4, 'nilai' => 3.0, 'tanggal_penilaian' => Carbon::now()->subDays(10)->toDateString(), 'keterangan' => 'Evaluasi Kebijakan Garansi: Perlu konfirmasi atasan jika ada retur', 'created_at' => Carbon::now()->subDays(10), 'updated_at' => Carbon::now()->subDays(10)],

            ['id' => 10, 'supplier_id' => 5, 'detail_penerimaan_pembelian_id' => null, 'kriteria_supplier_id' => 3, 'nilai' => 4.0, 'tanggal_penilaian' => Carbon::now()->subDays(10)->toDateString(), 'keterangan' => 'Evaluasi Kecepatan Respon: Fast respon via telepon kantor', 'created_at' => Carbon::now()->subDays(10), 'updated_at' => Carbon::now()->subDays(10)],
            ['id' => 11, 'supplier_id' => 5, 'detail_penerimaan_pembelian_id' => null, 'kriteria_supplier_id' => 4, 'nilai' => 5.0, 'tanggal_penilaian' => Carbon::now()->subDays(10)->toDateString(), 'keterangan' => 'Evaluasi Kebijakan Garansi: Garansi ganti rugi 100% jika produk rusak', 'created_at' => Carbon::now()->subDays(10), 'updated_at' => Carbon::now()->subDays(10)],
        ];
        DB::table('riwayat_penilaian_supplier')->insert($penilaian);

        // ── 19. PEMBAYARAN HUTANG KE SUPPLIER ─────────────────────────────────
        $bayarHutang = [
            // Pelunasan Faktur 1
            ['id' => 1, 'pembelian_id' => 1, 'tanggal_bayar' => Carbon::now()->subDays(10), 'jumlah_bayar' => 6250000.00, 'user_id' => 1, 'catatan' => 'Pelunasan via Transfer Bank BCA', 'created_at' => Carbon::now()->subDays(10), 'updated_at' => Carbon::now()->subDays(10)],
            // Cicilan 1 Faktur 3
            ['id' => 2, 'pembelian_id' => 3, 'tanggal_bayar' => Carbon::now()->subDays(2), 'jumlah_bayar' => 2000000.00, 'user_id' => 1, 'catatan' => 'Uang muka cicilan termin 1 via Tunai', 'created_at' => Carbon::now()->subDays(2), 'updated_at' => Carbon::now()->subDays(2)],
        ];
        DB::table('pembayaran_hutang')->insert($bayarHutang);

        // ── 20. PENJUALAN (Lunas Tunai, Kredit Piutang Kontraktor, Kirim Bertahap) ──
        $penjualan = [
            // 1. Penjualan Tunai di Toko (Langsung Lunas, Barang Langsung Dibawa)
            [
                'id' => 1, 'no_nota' => 'NOT-202611-001', 'user_id' => 2, 'customer_id' => null, // Pelanggan umum
                'tanggal' => Carbon::now()->subDays(5), 'ongkos_kirim' => 0.00, 'diskon' => 5000.00, 'total' => 205000.00,
                'status' => 'selesai', 'status_pengiriman' => 'seluruh', 'status_pembayaran' => 'lunas',
                'created_at' => Carbon::now()->subDays(5), 'updated_at' => Carbon::now()->subDays(5),
            ],
            // 2. Penjualan B2B Kredit ke Kontraktor (Tempo 30 Hari, Pengiriman Ekspedisi Toko)
            [
                'id' => 2, 'no_nota' => 'NOT-202611-002', 'user_id' => 2, 'customer_id' => 1, // CV Citra Karya
                'tanggal' => Carbon::now()->subDays(3), 'ongkos_kirim' => 100000.00, 'diskon' => 50000.00, 'total' => 3050000.00,
                'status' => 'proses', 'status_pengiriman' => 'dikirim_sebagian', 'status_pembayaran' => 'sebagian',
                'created_at' => Carbon::now()->subDays(3), 'updated_at' => Carbon::now()->subDays(1),
            ],
            // 3. Penjualan Produk Paku Timbang (Nominal Bayar Rp 20.000)
            [
                'id' => 3, 'no_nota' => 'NOT-202611-003', 'user_id' => 3, 'customer_id' => 4, // Ibu Hendrawan
                'tanggal' => Carbon::now()->subDays(1), 'ongkos_kirim' => 0.00, 'diskon' => 0.00, 'total' => 155000.00,
                'status' => 'selesai', 'status_pengiriman' => 'seluruh', 'status_pembayaran' => 'lunas',
                'created_at' => Carbon::now()->subDays(1), 'updated_at' => Carbon::now()->subDays(1),
            ],
        ];
        DB::table('penjualan')->insert($penjualan);

        // ── 21. DETAIL PENJUALAN ──────────────────────────────────────────────
        $detailPenjualan = [
            // Nota 1
            ['id' => 1, 'penjualan_id' => 1, 'produk_id' => 1, 'qty' => 3.000, 'harga_satuan' => 68000.00, 'diskon' => 5000.00, 'nominal_bayar' => null, 'subtotal' => 199000.00, 'created_at' => Carbon::now()->subDays(5), 'updated_at' => Carbon::now()->subDays(5)],
            ['id' => 2, 'penjualan_id' => 1, 'produk_id' => 2, 'qty' => 3.000, 'harga_satuan' => 2000.00,  'diskon' => 0.00,    'nominal_bayar' => null, 'subtotal' => 6000.00,   'created_at' => Carbon::now()->subDays(5), 'updated_at' => Carbon::now()->subDays(5)],
            // Nota 2 (Proyek Kontraktor: Semen 20 Sak, Besi 20 Batang)
            ['id' => 3, 'penjualan_id' => 2, 'produk_id' => 1, 'qty' => 20.000, 'harga_satuan' => 68000.00, 'diskon' => 20000.00, 'nominal_bayar' => null, 'subtotal' => 1340000.00, 'created_at' => Carbon::now()->subDays(3), 'updated_at' => Carbon::now()->subDays(3)],
            ['id' => 4, 'penjualan_id' => 2, 'produk_id' => 4, 'qty' => 20.000, 'harga_satuan' => 82000.00, 'diskon' => 30000.00, 'nominal_bayar' => null, 'subtotal' => 1610000.00, 'created_at' => Carbon::now()->subDays(3), 'updated_at' => Carbon::now()->subDays(3)],
            // Nota 3 (Avitex + Paku Timbang Beli Rp 20.000 dapat 1.111 kg)
            ['id' => 5, 'penjualan_id' => 3, 'produk_id' => 6, 'qty' => 1.000, 'harga_satuan' => 135000.00, 'diskon' => 0.00, 'nominal_bayar' => null, 'subtotal' => 135000.00, 'created_at' => Carbon::now()->subDays(1), 'updated_at' => Carbon::now()->subDays(1)],
            ['id' => 6, 'penjualan_id' => 3, 'produk_id' => 7, 'qty' => 1.111, 'harga_satuan' => 18000.00,  'diskon' => 0.00, 'nominal_bayar' => 20000.00, 'subtotal' => 20000.00, 'created_at' => Carbon::now()->subDays(1), 'updated_at' => Carbon::now()->subDays(1)],
        ];
        DB::table('detail_penjualan')->insert($detailPenjualan);

        // ── 22. PENGIRIMAN PENJUALAN ──────────────────────────────────────────
        $pengiriman = [
            // Surat Jalan Kirim Tahap 1 untuk CV Citra Karya
            [
                'id' => 1, 'penjualan_id' => 2, 'tanggal_kirim' => Carbon::now()->subDays(2),
                'user_id' => 1, 'catatan' => 'Pengiriman armada truk 1: muat 20 sak semen gresik terlebih dahulu ke lokasi Citraland',
                'created_at' => Carbon::now()->subDays(2), 'updated_at' => Carbon::now()->subDays(2),
            ]
        ];
        DB::table('pengiriman_penjualan')->insert($pengiriman);

        // ── 23. DETAIL PENGIRIMAN PENJUALAN ───────────────────────────────────
        $detailPengiriman = [
            ['id' => 1, 'pengiriman_penjualan_id' => 1, 'detail_penjualan_id' => 3, 'qty_dikirim' => 20.000, 'created_at' => Carbon::now()->subDays(2), 'updated_at' => Carbon::now()->subDays(2)],
        ];
        DB::table('detail_pengiriman_penjualan')->insert($detailPengiriman);

        // ── 24. PEMBAYARAN PIUTANG PELANGGAN ──────────────────────────────────
        $bayarPiutang = [
            // CV Citra Karya mencicil uang muka Rp 1.500.000
            [
                'id' => 1, 'penjualan_id' => 2, 'tanggal_bayar' => Carbon::now()->subDays(1),
                'jumlah_bayar' => 1500000.00, 'user_id' => 2, 'catatan' => 'Uang muka pembayaran tempo proyek Citraland',
                'created_at' => Carbon::now()->subDays(1), 'updated_at' => Carbon::now()->subDays(1),
            ]
        ];
        DB::table('pembayaran_piutang')->insert($bayarPiutang);

        // ── 25. STOK OPNAME ───────────────────────────────────────────────────
        $stokOpname = [
            // Sesi 1: Sudah Disetujui / Reconciled
            [
                'id' => 1, 'tanggal' => Carbon::now()->subDays(8), 'user_id' => 1,
                'catatan' => 'Audit rutin fisik gudang awal bulan November 2026', 'status' => 'disesuaikan',
                'created_at' => Carbon::now()->subDays(8), 'updated_at' => Carbon::now()->subDays(8),
            ],
            // Sesi 2: Menunggu Persetujuan Pemilik Toko
            [
                'id' => 2, 'tanggal' => Carbon::now()->subDays(1), 'user_id' => 2,
                'catatan' => 'Pengecekan fisik semen dan paku timbang oleh kasir shift pagi', 'status' => 'menunggu_persetujuan',
                'created_at' => Carbon::now()->subDays(1), 'updated_at' => Carbon::now()->subDays(1),
            ],
        ];
        DB::table('stok_opname')->insert($stokOpname);

        // ── 26. DETAIL STOK OPNAME ────────────────────────────────────────────
        $detailOpname = [
            // Sesi 1 (Reconciled: ada selisih susut 1 batang besi)
            ['id' => 1, 'stok_opname_id' => 1, 'produk_id' => 4, 'stok_sistem' => 16.000, 'stok_fisik' => 15.000, 'selisih' => -1.000, 'created_at' => Carbon::now()->subDays(8), 'updated_at' => Carbon::now()->subDays(8)],
            // Sesi 2 (Menunggu Persetujuan: Semen Gresik pas, Paku ada selisih timbangan)
            ['id' => 2, 'stok_opname_id' => 2, 'produk_id' => 1, 'stok_sistem' => 150.000, 'stok_fisik' => 150.000, 'selisih' => 0.000, 'created_at' => Carbon::now()->subDays(1), 'updated_at' => Carbon::now()->subDays(1)],
            ['id' => 3, 'stok_opname_id' => 2, 'produk_id' => 7, 'stok_sistem' => 95.000,  'stok_fisik' => 92.450,  'selisih' => -2.550, 'created_at' => Carbon::now()->subDays(1), 'updated_at' => Carbon::now()->subDays(1)],
        ];
        DB::table('detail_stok_opname')->insert($detailOpname);

        // ── 27. SAW NORMALISASI (Hasil Normalisasi AHP-SAW untuk Alternatif Semen) ──
        $sawNorm = [
            // SUP-001 (ProdukSupplier Id: 1)
            ['id' => 1, 'produk_supplier_id' => 1, 'kriteria_supplier_id' => 1, 'nilai_asli' => 62500, 'nilai_normalisasi' => 0.9840, 'created_at' => $now, 'updated_at' => $now], // Harga (Cost: 61500/62500)
            ['id' => 2, 'produk_supplier_id' => 1, 'kriteria_supplier_id' => 2, 'nilai_asli' => 5.0,   'nilai_normalisasi' => 1.0000, 'created_at' => $now, 'updated_at' => $now], // Kualitas (Benefit)
            ['id' => 3, 'produk_supplier_id' => 1, 'kriteria_supplier_id' => 3, 'nilai_asli' => 5.0,   'nilai_normalisasi' => 1.0000, 'created_at' => $now, 'updated_at' => $now], // Respon (Benefit)
            ['id' => 4, 'produk_supplier_id' => 1, 'kriteria_supplier_id' => 4, 'nilai_asli' => 4.0,   'nilai_normalisasi' => 0.8000, 'created_at' => $now, 'updated_at' => $now], // Garansi (Benefit)
            ['id' => 5, 'produk_supplier_id' => 1, 'kriteria_supplier_id' => 5, 'nilai_asli' => 2.0,   'nilai_normalisasi' => 1.0000, 'created_at' => $now, 'updated_at' => $now], // Waktu Kirim (Cost: 2 hari)
            ['id' => 6, 'produk_supplier_id' => 1, 'kriteria_supplier_id' => 6, 'nilai_asli' => 30.0,  'nilai_normalisasi' => 1.0000, 'created_at' => $now, 'updated_at' => $now], // Tempo 30 Hari

            // SUP-003 (ProdukSupplier Id: 2)
            ['id' => 7, 'produk_supplier_id' => 2, 'kriteria_supplier_id' => 1, 'nilai_asli' => 63500, 'nilai_normalisasi' => 0.9685, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8, 'produk_supplier_id' => 2, 'kriteria_supplier_id' => 2, 'nilai_asli' => 4.0,   'nilai_normalisasi' => 0.8000, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 9, 'produk_supplier_id' => 2, 'kriteria_supplier_id' => 3, 'nilai_asli' => 3.0,   'nilai_normalisasi' => 0.6000, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 10, 'produk_supplier_id' => 2, 'kriteria_supplier_id' => 4, 'nilai_asli' => 3.0,  'nilai_normalisasi' => 0.6000, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 11, 'produk_supplier_id' => 2, 'kriteria_supplier_id' => 5, 'nilai_asli' => 3.0,  'nilai_normalisasi' => 0.6667, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 12, 'produk_supplier_id' => 2, 'kriteria_supplier_id' => 6, 'nilai_asli' => 14.0, 'nilai_normalisasi' => 0.4667, 'created_at' => $now, 'updated_at' => $now],

            // SUP-005 (ProdukSupplier Id: 3)
            ['id' => 13, 'produk_supplier_id' => 3, 'kriteria_supplier_id' => 1, 'nilai_asli' => 61500, 'nilai_normalisasi' => 1.0000, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 14, 'produk_supplier_id' => 3, 'kriteria_supplier_id' => 2, 'nilai_asli' => 4.0,   'nilai_normalisasi' => 0.8000, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 15, 'produk_supplier_id' => 3, 'kriteria_supplier_id' => 3, 'nilai_asli' => 4.0,   'nilai_normalisasi' => 0.8000, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 16, 'produk_supplier_id' => 3, 'kriteria_supplier_id' => 4, 'nilai_asli' => 5.0,   'nilai_normalisasi' => 1.0000, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 17, 'produk_supplier_id' => 3, 'kriteria_supplier_id' => 5, 'nilai_asli' => 4.0,   'nilai_normalisasi' => 0.5000, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 18, 'produk_supplier_id' => 3, 'kriteria_supplier_id' => 6, 'nilai_asli' => 7.0,  'nilai_normalisasi' => 0.2333, 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('saw_normalisasi')->insert($sawNorm);
    }
}
