<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SatuanProduk extends Model
{
    protected $table = 'satuan_produk';

    protected $fillable = ['nama_satuan'];

    public function produk()
    {
        return $this->hasMany(Produk::class, 'satuan_dasar_id');
    }

    public function masterKonversiAsal()
    {
        return $this->hasMany(MasterKonversi::class, 'satuan_asal_id');
    }

    public function masterKonversiTujuan()
    {
        return $this->hasMany(MasterKonversi::class, 'satuan_tujuan_id');
    }
}
