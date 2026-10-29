<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualan';

    protected $fillable = [
        'no_nota', 'user_id', 'customer_id', 'tanggal',
        'ongkos_kirim', 'diskon', 'total',
        'status', 'status_pengiriman', 'status_pembayaran',
    ];

    protected $casts = [
        'tanggal'      => 'datetime',
        'ongkos_kirim' => 'decimal:2',
        'diskon'       => 'decimal:2',
        'total'        => 'decimal:2',
    ];

    // Accessor: total sudah dibayar
    public function getTotalDibayarAttribute()
    {
        return $this->pembayaranPiutang->sum('jumlah_bayar');
    }

    // Accessor: sisa piutang
    public function getSisaPiutangAttribute()
    {
        return $this->total - $this->total_dibayar;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function detailPenjualan()
    {
        return $this->hasMany(DetailPenjualan::class);
    }

    public function pengirimanPenjualan()
    {
        return $this->hasMany(PengirimanPenjualan::class);
    }

    public function pembayaranPiutang()
    {
        return $this->hasMany(PembayaranPiutang::class);
    }
}
