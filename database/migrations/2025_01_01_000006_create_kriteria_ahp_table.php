<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kriteria_ahp', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kriteria', 10)->unique();
            $table->string('nama_kriteria', 100);
            $table->enum('jenis_kriteria', ['benefit', 'cost']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kriteria_ahp');
    }
};
