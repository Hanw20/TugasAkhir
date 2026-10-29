<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPengirimanPenjualan extends Model
{
    protected $table = 'detail_pengiriman_penjualan';

    protected $fillable = [
        'pengiriman_penjualan_id', 'detail_penjualan_id', 'qty_dikirim',
    ];

    protected $casts = ['qty_dikirim' => 'decimal:3'];

    public function pengirimanPenjualan()
    {
        return $this->belongsTo(PengirimanPenjualan::class);
    }

    public function detailPenjualan()
    {
        return $this->belongsTo(DetailPenjualan::class);
    }
}
