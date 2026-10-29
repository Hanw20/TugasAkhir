<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';

    protected $fillable = ['nama_customer', 'no_telepon', 'alamat'];

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class);
    }
}
