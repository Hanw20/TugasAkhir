<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterKonversi extends Model
{
    protected $table = 'master_konversi';

    protected $fillable = ['satuan_asal_id', 'satuan_tujuan_id', 'nilai_konversi'];

    public function satuanAsal()
    {
        return $this->belongsTo(SatuanProduk::class, 'satuan_asal_id');
    }

    public function satuanTujuan()
    {
        return $this->belongsTo(SatuanProduk::class, 'satuan_tujuan_id');
    }

    public function konversi()
    {
        return $this->hasMany(Konversi::class);
    }
}
