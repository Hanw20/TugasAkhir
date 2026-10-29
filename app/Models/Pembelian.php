<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $table = 'pembelian';

    protected $fillable = [
        'no_faktur', 'supplier_id', 'user_id', 'tanggal',
        'total', 'tanggal_jatuh_tempo',
        'status', 'status_penerimaan', 'status_pembayaran',
    ];

    protected $casts = [
        'tanggal'             => 'date',
        'tanggal_jatuh_tempo' => 'datetime',
        'total'               => 'decimal:2',
    ];

    // Accessor: total sudah dibayar
    public function getTotalDibayarAttribute()
    {
        return $this->pembayaranHutang->sum('jumlah_bayar');
    }

    // Accessor: sisa hutang
    public function getSisaHutangAttribute()
    {
        return $this->total - $this->total_dibayar;
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detailPembelian()
    {
        return $this->hasMany(DetailPembelian::class);
    }

    public function penerimaanPembelian()
    {
        return $this->hasMany(PenerimaanPembelian::class);
    }

    public function pembayaranHutang()
    {
        return $this->hasMany(PembayaranHutang::class);
    }
}
