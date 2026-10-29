<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->string('no_nota', 50)->unique();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->dateTime('tanggal');
            $table->decimal('ongkos_kirim', 12, 2)->default(0);
            $table->decimal('diskon', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('status', ['proses', 'kirim', 'selesai', 'batal'])->default('proses');
            $table->enum('status_pengiriman', ['belum_dikirim', 'dikirim_sebagian', 'seluruh'])->default('belum_dikirim');
            $table->enum('status_pembayaran', ['belum_dibayar', 'sebagian', 'lunas'])->default('belum_dibayar');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};
