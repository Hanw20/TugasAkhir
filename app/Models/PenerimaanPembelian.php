<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenerimaanPembelian extends Model
{
    protected $table = 'penerimaan_pembelian';

    protected $fillable = [
        'pembelian_id', 'tanggal_terima', 'user_id', 'catatan',
    ];

    protected $casts = ['tanggal_terima' => 'datetime'];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detailPenerimaan()
    {
        return $this->hasMany(DetailPenerimaanPembelian::class);
    }
}
