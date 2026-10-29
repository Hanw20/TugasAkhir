<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembelian', function (Blueprint $table) {
            $table->id();
            $table->string('no_faktur', 50)->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->date('tanggal');
            $table->decimal('total', 12, 2)->default(0);
            $table->dateTime('tanggal_jatuh_tempo')->nullable();
            $table->enum('status', ['proses', 'dikirim', 'selesai', 'batal'])->default('proses');
            $table->enum('status_penerimaan', ['belum_diterima', 'sebagian', 'terima_penuh'])->default('belum_diterima');
            $table->enum('status_pembayaran', ['belum_dibayar', 'sebagian', 'lunas'])->default('belum_dibayar');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelian');
    }
};
