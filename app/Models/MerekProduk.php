<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerekProduk extends Model
{
    protected $table = 'merek_produk';

    protected $fillable = ['nama_merek'];

    public function produk()
    {
        return $this->hasMany(Produk::class, 'merek_id');
    }
}
