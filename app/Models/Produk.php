<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';

    protected $fillable = [
        'kode_produk', 'nama_produk', 'kategori_produk_id', 'merek_id',
        'satuan_dasar_id', 'metode_jual', 'harga_beli', 'harga_beli_terakhir',
        'harga_jual', 'stok', 'stok_minimum',
        'stok_onorder_supplier', 'stok_onorder_customer',
    ];

    protected $casts = [
        'harga_beli'             => 'decimal:2',
        'harga_beli_terakhir'    => 'decimal:2',
        'harga_jual'             => 'decimal:2',
        'stok'                   => 'decimal:3',
        'stok_minimum'           => 'decimal:3',
        'stok_onorder_supplier'  => 'decimal:3',
        'stok_onorder_customer'  => 'decimal:3',
    ];

    // Accessor: apakah stok menipis?
    public function getStokMenipisAttribute(): bool
    {
        return $this->stok <= $this->stok_minimum;
    }

    // Relasi ke master
    public function kategori()
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_produk_id');
    }

    public function merek()
    {
        return $this->belongsTo(MerekProduk::class, 'merek_id');
    }

    public function satuanDasar()
    {
        return $this->belongsTo(SatuanProduk::class, 'satuan_dasar_id');
    }

    // Relasi ke supplier (many-to-many)
    public function supplier()
    {
        return $this->belongsToMany(Supplier::class, 'produk_supplier');
    }

    // Relasi ke transaksi
    public function detailPembelian()
    {
        return $this->hasMany(DetailPembelian::class);
    }

    public function detailPenjualan()
    {
        return $this->hasMany(DetailPenjualan::class);
    }

    // Relasi ke konversi
    public function konversiAsal()
    {
        return $this->hasMany(Konversi::class, 'produk_asal_id');
    }

    public function konversiTujuan()
    {
        return $this->hasMany(Konversi::class, 'produk_tujuan_id');
    }

    public function detailStokOpname()
    {
        return $this->hasMany(DetailStokOpname::class);
    }
        // Menghitung Harga Beli Rata-Rata secara riil dari histori transaksi penerimaan
    public function getHargaBeliRataRataAttribute()
    {
        // Ambil semua detail pembelian untuk produk ini yang sudah ada penerimaan fisiknya
        $details = $this->detailPembelian()->with('detailPenerimaan')->get();

        $totalNilaiFisik = 0;
        $totalQtyFisik = 0;

        foreach ($details as $dp) {
            $qtyDiterima = $dp->detailPenerimaan->sum('qty_diterima');
            if ($qtyDiterima > 0) {
                $totalNilaiFisik += ($qtyDiterima * $dp->harga_satuan);
                $totalQtyFisik += $qtyDiterima;
            }
        }

        // Jika pernah ada penerimaan dari supplier, hitung rata-ratanya
        if ($totalQtyFisik > 0) {
            return round($totalNilaiFisik / $totalQtyFisik, 2);
        }

        // Jika belum pernah ada transaksi penerimaan, gunakan harga beli default
        return (float) $this->harga_beli;
    }
}
