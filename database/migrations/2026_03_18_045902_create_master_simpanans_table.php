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
        Schema::create('master_simpanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained()->cascadeOnDelete();
            $table->decimal('simpanan_pokok', 15, 2)->default(0);
            $table->decimal('simpanan_wajib', 15, 2)->default(0);
            $table->decimal('simpanan_sukarela', 15, 2)->default(0);
            $table->date('tanggal_mulai');
            $table->date('tanggal_akhir')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_simpanans');
    }
};
