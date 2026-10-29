<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPembelian extends Model
{
    protected $table = 'detail_pembelian';

    protected $fillable = [
        'pembelian_id', 'produk_id', 'qty', 'harga_satuan', 'subtotal',
    ];

    protected $casts = [
        'qty'          => 'decimal:3',
        'harga_satuan' => 'decimal:2',
        'subtotal'     => 'decimal:2',
    ];

    // Accessor: qty yang sudah diterima
    public function getQtyDiterimaAttribute()
    {
        return $this->detailPenerimaan->sum('qty_diterima');
    }

    // Accessor: sisa qty yang belum diterima
    public function getSisaQtyAttribute()
    {
        return $this->qty - $this->qty_diterima;
    }

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function detailPenerimaan()
    {
        return $this->hasMany(DetailPenerimaanPembelian::class);
    }

    public function riwayatPenilaian()
    {
        return $this->hasMany(RiwayatPenilaianSupplier::class, 'detail_penerimaan_pembelian_id');
    }
}
