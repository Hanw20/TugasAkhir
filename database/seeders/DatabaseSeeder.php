<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\KriteriaSupplier;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DummyDataSeeder::class);
    }
}
