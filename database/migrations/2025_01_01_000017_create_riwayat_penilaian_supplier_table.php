<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_penilaian_supplier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            // Custom constraint name agar tidak melebihi 64 karakter limit MySQL
            $table->unsignedBigInteger('detail_penerimaan_pembelian_id');
            $table->foreign('detail_penerimaan_pembelian_id', 'fk_rps_detail_penerimaan')
                  ->references('id')->on('detail_penerimaan_pembelian')->cascadeOnDelete();
            $table->foreignId('kriteria_ahp_id')->constrained('kriteria_ahp')->restrictOnDelete();
            $table->decimal('nilai', 12, 2);
            $table->date('tanggal_penilaian');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_penilaian_supplier');
    }
};
