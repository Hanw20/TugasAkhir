<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_konversi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('satuan_asal_id')->constrained('satuan_produk')->restrictOnDelete();
            $table->foreignId('satuan_tujuan_id')->constrained('satuan_produk')->restrictOnDelete();
            $table->decimal('nilai_konversi', 15, 4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_konversi');
    }
};
