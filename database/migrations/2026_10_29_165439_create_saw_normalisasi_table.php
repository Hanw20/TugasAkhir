<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('saw_normalisasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_supplier_id')->constrained('produk_supplier')->cascadeOnDelete();
            $table->foreignId('kriteria_ahp_id')->constrained('kriteria_ahp')->cascadeOnDelete();
            $table->decimal('nilai_asli', 15, 4)->default(0);
            $table->decimal('nilai_normalisasi', 10, 4)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saw_normalisasi');
    }
};
