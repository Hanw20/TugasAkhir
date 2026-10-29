<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konversi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_konversi_id')->constrained('master_konversi')->restrictOnDelete();
            $table->foreignId('produk_asal_id')->constrained('produk')->restrictOnDelete();
            $table->decimal('qty_asal_dipakai', 15, 3);
            $table->foreignId('produk_tujuan_id')->constrained('produk')->restrictOnDelete();
            $table->decimal('qty_hasil_aktual', 15, 3);
            $table->date('tanggal');
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('catatan', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konversi');
    }
};
