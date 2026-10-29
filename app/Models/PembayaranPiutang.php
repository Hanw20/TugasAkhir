<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranPiutang extends Model
{
    protected $table = 'pembayaran_piutang';

    protected $fillable = [
        'penjualan_id', 'tanggal_bayar', 'jumlah_bayar', 'user_id', 'catatan',
    ];

    protected $casts = [
        'tanggal_bayar' => 'datetime',
        'jumlah_bayar'  => 'decimal:2',
    ];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
