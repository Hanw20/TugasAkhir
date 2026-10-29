<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_penerimaan_pembelian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penerimaan_pembelian_id')->constrained('penerimaan_pembelian')->cascadeOnDelete();
            $table->foreignId('detail_pembelian_id')->constrained('detail_pembelian')->cascadeOnDelete();
            $table->decimal('qty_diterima', 15, 3);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penerimaan_pembelian');
    }
};
