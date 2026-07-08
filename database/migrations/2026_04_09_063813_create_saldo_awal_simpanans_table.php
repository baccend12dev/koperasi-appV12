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
        Schema::create('saldo_awal_simpanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggotas')->onDelete('cascade');
            $table->decimal('pokok', 15, 2)->default(0);
            $table->decimal('wajib', 15, 2)->default(0);
            $table->decimal('sukarela', 15, 2)->default(0);
            $table->decimal('bunga', 15, 2)->default(0);
            $table->decimal('shu', 15, 2)->default(0);
            $table->decimal('hadir_rat', 15, 2)->default(0);
            $table->decimal('nominal', 15, 2);
            $table->date('tanggal_awal');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saldo_awal_simpanans');
    }
};
