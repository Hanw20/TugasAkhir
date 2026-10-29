<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    protected $table = 'detail_penjualan';

    protected $fillable = [
        'penjualan_id', 'produk_id', 'qty', 'harga_satuan',
        'diskon', 'nominal_bayar', 'subtotal',
    ];

    protected $casts = [
        'qty'          => 'decimal:3',
        'harga_satuan' => 'decimal:2',
        'diskon'       => 'decimal:2',
        'nominal_bayar'=> 'decimal:2',
        'subtotal'     => 'decimal:2',
    ];

    // Accessor: qty yang sudah dikirim
    public function getQtyDikirimAttribute()
    {
        return $this->detailPengiriman->sum('qty_dikirim');
    }

    // Accessor: sisa qty yang belum dikirim
    public function getSisaQtyAttribute()
    {
        return $this->qty - $this->qty_dikirim;
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function detailPengiriman()
    {
        return $this->hasMany(DetailPengirimanPenjualan::class);
    }
}
