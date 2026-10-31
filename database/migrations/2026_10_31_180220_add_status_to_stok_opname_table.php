<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stok_opname', function (Blueprint $table) {
            $table->enum('status', ['menunggu_persetujuan', 'disesuaikan'])
                  ->default('menunggu_persetujuan')
                  ->after('catatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stok_opname', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};