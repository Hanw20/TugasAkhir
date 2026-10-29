<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perbandingan_kriteria_ahp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_perhitungan_ahp_id')->constrained('sesi_perhitungan_ahp')->cascadeOnDelete();
            $table->foreignId('kriteria_a_id')->constrained('kriteria_ahp')->restrictOnDelete();
            $table->foreignId('kriteria_b_id')->constrained('kriteria_ahp')->restrictOnDelete();
            $table->decimal('nilai_perbandingan', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perbandingan_kriteria_ahp');
    }
};
