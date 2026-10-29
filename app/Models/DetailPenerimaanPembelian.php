<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenerimaanPembelian extends Model
{
    protected $table = 'detail_penerimaan_pembelian';

    protected $fillable = [
        'penerimaan_pembelian_id', 'detail_pembelian_id', 'qty_diterima',
    ];

    protected $casts = ['qty_diterima' => 'decimal:3'];

    public function penerimaanPembelian()
    {
        return $this->belongsTo(PenerimaanPembelian::class);
    }

    public function detailPembelian()
    {
        return $this->belongsTo(DetailPembelian::class);
    }

    public function riwayatPenilaian()
    {
        return $this->hasMany(RiwayatPenilaianSupplier::class, 'detail_penerimaan_pembelian_id');
    }
}
