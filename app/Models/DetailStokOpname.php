<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailStokOpname extends Model
{
    protected $table = 'detail_stok_opname';

    protected $fillable = [
        'stok_opname_id', 'produk_id', 'stok_sistem', 'stok_fisik', 'selisih',
    ];

    protected $casts = [
        'stok_sistem' => 'decimal:3',
        'stok_fisik'  => 'decimal:3',
        'selisih'     => 'decimal:3',
    ];

    public function stokOpname()
    {
        return $this->belongsTo(StokOpname::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
