<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_hutang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id')->constrained('pembelian')->cascadeOnDelete();
            $table->dateTime('tanggal_bayar');
            $table->decimal('jumlah_bayar', 12, 2);
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('catatan', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_hutang');
    }
};
