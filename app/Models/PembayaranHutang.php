<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranHutang extends Model
{
    protected $table = 'pembayaran_hutang';

    protected $fillable = [
        'pembelian_id', 'tanggal_bayar', 'jumlah_bayar', 'user_id', 'catatan',
    ];

    protected $casts = [
        'tanggal_bayar' => 'datetime',
        'jumlah_bayar'  => 'decimal:2',
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
