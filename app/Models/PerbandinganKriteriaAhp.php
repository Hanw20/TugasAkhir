<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerbandinganKriteriaAhp extends Model
{
    protected $table = 'perbandingan_kriteria_ahp';

    protected $fillable = [
        'sesi_perhitungan_ahp_id', 'kriteria_a_id',
        'kriteria_b_id', 'nilai_perbandingan',
    ];

    public function sesi()
    {
        return $this->belongsTo(SesiPerhitunganAhp::class, 'sesi_perhitungan_ahp_id');
    }

    public function kriteriaA()
    {
        return $this->belongsTo(KriteriaAhp::class, 'kriteria_a_id');
    }

    public function kriteriaB()
    {
        return $this->belongsTo(KriteriaAhp::class, 'kriteria_b_id');
    }
}
