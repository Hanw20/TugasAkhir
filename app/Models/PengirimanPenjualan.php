<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengirimanPenjualan extends Model
{
    protected $table = 'pengiriman_penjualan';

    protected $fillable = [
        'penjualan_id', 'tanggal_kirim', 'user_id', 'catatan',
    ];

    protected $casts = ['tanggal_kirim' => 'datetime'];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detailPengiriman()
    {
        return $this->hasMany(DetailPengirimanPenjualan::class);
    }
}
