<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SawNormalisasi extends Model
{
    protected $table = 'saw_normalisasi';

    protected $fillable = [
        'produk_supplier_id',
        'kriteria_supplier_id',
        'nilai_asli',
        'nilai_normalisasi',
    ];

    public function kriteria()
    {
        return $this->belongsTo(KriteriaSupplier::class, 'kriteria_supplier_id');
    }
}
