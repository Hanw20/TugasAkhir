<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesi_perhitungan_ahp', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sesi', 100);
            $table->dateTime('tanggal_hitung');
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_perhitungan_ahp');
    }
};
