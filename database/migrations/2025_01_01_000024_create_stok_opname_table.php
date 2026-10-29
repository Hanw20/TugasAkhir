<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_opname', function (Blueprint $table) {
            $table->id();
            $table->dateTime('tanggal');
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('catatan', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_opname');
    }
};
