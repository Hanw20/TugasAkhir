<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'suppliers';

    protected $fillable = [
        'kode_supplier', 'nama_supplier', 'alamat',
        'nomor_telepon', 'email',
    ];

    public function produk()
    {
        return $this->belongsToMany(Produk::class, 'produk_supplier');
    }

    public function pembelian()
    {
        return $this->hasMany(Pembelian::class);
    }

    public function riwayatPenilaian()
    {
        return $this->hasMany(RiwayatPenilaianSupplier::class);
    }
}
