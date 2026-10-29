<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['email_verified_at' => 'datetime'];

    // Helper roles
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }

    // Relasi
    public function pembelian()
    {
        return $this->hasMany(Pembelian::class);
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class);
    }

    public function konversi()
    {
        return $this->hasMany(Konversi::class);
    }

    public function sesiPerhitunganAhp()
    {
        return $this->hasMany(SesiPerhitunganAhp::class);
    }

    public function stokOpname()
    {
        return $this->hasMany(StokOpname::class);
    }
}
