<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokOpname extends Model
{
    protected $table = 'stok_opname';

    protected $fillable = ['tanggal', 'user_id', 'catatan'];

    protected $casts = ['tanggal' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detailStokOpname()
    {
        return $this->hasMany(DetailStokOpname::class);
    }
}
