<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\KriteriaAhp;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Default Users ──────────────────────────────────────────────────
        User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@tbb.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Kasir 1',
            'email'    => 'kasir@tbb.com',
            'password' => Hash::make('kasir123'),
            'role'     => 'kasir',
        ]);

        // ── 5 Kriteria AHP Default ─────────────────────────────────────────
        $kriteria = [
            ['kode_kriteria' => 'K1', 'nama_kriteria' => 'Harga',          'jenis_kriteria' => 'cost'],
            ['kode_kriteria' => 'K2', 'nama_kriteria' => 'Kualitas',        'jenis_kriteria' => 'benefit'],
            ['kode_kriteria' => 'K3', 'nama_kriteria' => 'Pelayanan',       'jenis_kriteria' => 'benefit'],
            ['kode_kriteria' => 'K4', 'nama_kriteria' => 'Waktu Pengiriman','jenis_kriteria' => 'cost'],
            ['kode_kriteria' => 'K5', 'nama_kriteria' => 'Tempo Pembayaran','jenis_kriteria' => 'benefit'],
        ];

        foreach ($kriteria as $k) {
            \Illuminate\Support\Facades\DB::table('kriteria_ahp')->insert($k);
        }
    }
}
