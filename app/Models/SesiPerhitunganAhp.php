<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesiPerhitunganAhp extends Model
{
    protected $table = 'sesi_perhitungan_ahp';

    protected $fillable = [
        'nama_sesi',
        'tanggal_hitung',
        'user_id',
    ];

    protected $casts = [
        'tanggal_hitung' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function perbandingan()
    {
        return $this->hasMany(PerbandinganKriteriaAhp::class, 'sesi_perhitungan_ahp_id');
    }
}