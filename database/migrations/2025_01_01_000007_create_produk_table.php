<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('kode_produk', 50)->unique();
            $table->string('nama_produk', 100);
            $table->foreignId('kategori_produk_id')->constrained('kategori_produk')->restrictOnDelete();
            $table->foreignId('merek_id')->constrained('merek_produk')->restrictOnDelete();
            $table->foreignId('satuan_dasar_id')->constrained('satuan_produk')->restrictOnDelete();
            $table->enum('metode_jual', ['satuan', 'nominal_timbang'])->default('satuan');
            $table->decimal('harga_beli', 12, 2)->default(0);
            $table->decimal('harga_beli_terakhir', 12, 2)->default(0);
            $table->decimal('harga_jual', 12, 2)->default(0);
            $table->decimal('stok', 15, 3)->default(0);
            $table->decimal('stok_minimum', 15, 3)->default(0);
            $table->decimal('stok_onorder_supplier', 15, 3)->default(0);
            $table->decimal('stok_onorder_customer', 15, 3)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
