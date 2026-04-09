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
        Schema::table('transaksi_simpanans', function (Blueprint $table) {
            $table->dropForeign(['jenis_simpanan_id']);
            $table->dropColumn('jenis_simpanan_id');
            $table->dropColumn('amount');
            
            $table->decimal('simpanan_pokok', 15, 2)->default(0);
            $table->decimal('simpanan_wajib', 15, 2)->default(0);
            $table->decimal('simpanan_sukarela', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_simpanans', function (Blueprint $table) {
            $table->foreignId('jenis_simpanan_id')->nullable()->constrained('jenis_simpanans')->cascadeOnDelete();
            $table->decimal('amount', 15, 2)->default(0);
            
            $table->dropColumn('simpanan_pokok');
            $table->dropColumn('simpanan_wajib');
            $table->dropColumn('simpanan_sukarela');
        });
    }
};
