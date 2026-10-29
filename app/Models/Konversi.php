<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konversi extends Model
{
    protected $table = 'konversi';

    protected $fillable = [
        'master_konversi_id', 'produk_asal_id', 'qty_asal_dipakai',
        'produk_tujuan_id', 'qty_hasil_aktual', 'tanggal', 'user_id', 'catatan',
    ];

    protected $casts = ['tanggal' => 'date'];

    public function masterKonversi()
    {
        return $this->belongsTo(MasterKonversi::class);
    }

    public function produkAsal()
    {
        return $this->belongsTo(Produk::class, 'produk_asal_id');
    }

    public function produkTujuan()
    {
        return $this->belongsTo(Produk::class, 'produk_tujuan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
