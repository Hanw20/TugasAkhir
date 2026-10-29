<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_pengiriman_penjualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengiriman_penjualan_id')->constrained('pengiriman_penjualan')->cascadeOnDelete();
            $table->foreignId('detail_penjualan_id')->constrained('detail_penjualan')->cascadeOnDelete();
            $table->decimal('qty_dikirim', 15, 3);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pengiriman_penjualan');
    }
};
