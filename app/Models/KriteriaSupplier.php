<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KriteriaSupplier extends Model
{
    protected $table = 'kriteria_supplier';

    protected $fillable = ['kode_kriteria', 'nama_kriteria', 'jenis_kriteria'];

    public function perbandinganSebagaiA()
    {
        return $this->hasMany(PerbandinganKriteriaAhp::class, 'kriteria_a_id');
    }

    public function perbandinganSebagaiB()
    {
        return $this->hasMany(PerbandinganKriteriaAhp::class, 'kriteria_b_id');
    }

    public function riwayatPenilaianSupplier()
    {
        return $this->hasMany(RiwayatPenilaianSupplier::class, 'kriteria_supplier_id');
    }

    public function sawNormalisasi()
    {
        return $this->hasMany(SawNormalisasi::class, 'kriteria_supplier_id');
    }
}
