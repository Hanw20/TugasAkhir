<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_stok_opname', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stok_opname_id')->constrained('stok_opname')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk')->restrictOnDelete();
            $table->decimal('stok_sistem', 15, 3);
            $table->decimal('stok_fisik', 15, 3);
            $table->decimal('selisih', 15, 3);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_stok_opname');
    }
};
