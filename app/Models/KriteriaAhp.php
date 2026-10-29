<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KriteriaAhp extends Model
{
    protected $table = 'kriteria_ahp';

    protected $fillable = ['kode_kriteria', 'nama_kriteria', 'jenis_kriteria'];

    public function perbandinganSebagaiA()
    {
        return $this->hasMany(PerbandinganKriteriaAhp::class, 'kriteria_a_id');
    }

    public function perbandinganSebagaiB()
    {
        return $this->hasMany(PerbandinganKriteriaAhp::class, 'kriteria_b_id');
    }

    public function riwayatPenilaian()
    {
        return $this->hasMany(RiwayatPenilaianSupplier::class);
    }
}
