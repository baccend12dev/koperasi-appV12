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
            if (Schema::hasColumn('transaksi_simpanans', 'jenis_simpanan_id')) {
                // Only drop foreign key if it's not SQLite or if the foreign key exists
                if (config('database.default') !== 'sqlite') {
                    $table->dropForeign(['jenis_simpanan_id']);
                }
                $table->dropColumn('jenis_simpanan_id');
            }
            if (Schema::hasColumn('transaksi_simpanans', 'amount')) {
                $table->dropColumn('amount');
            }
            
            if (!Schema::hasColumn('transaksi_simpanans', 'simpanan_pokok')) {
                $table->decimal('simpanan_pokok', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('transaksi_simpanans', 'simpanan_wajib')) {
                $table->decimal('simpanan_wajib', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('transaksi_simpanans', 'simpanan_sukarela')) {
                $table->decimal('simpanan_sukarela', 15, 2)->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_simpanans', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksi_simpanans', 'jenis_simpanan_id')) {
                $table->foreignId('jenis_simpanan_id')->nullable()->constrained('jenis_simpanans')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('transaksi_simpanans', 'amount')) {
                $table->decimal('amount', 15, 2)->default(0);
            }
            
            if (Schema::hasColumn('transaksi_simpanans', 'simpanan_pokok')) {
                $table->dropColumn('simpanan_pokok');
            }
            if (Schema::hasColumn('transaksi_simpanans', 'simpanan_wajib')) {
                $table->dropColumn('simpanan_wajib');
            }
            if (Schema::hasColumn('transaksi_simpanans', 'simpanan_sukarela')) {
                $table->dropColumn('simpanan_sukarela');
            }
        });
    }
};
