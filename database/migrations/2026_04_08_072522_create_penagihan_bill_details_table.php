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
        Schema::create('penagihan_bill_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penagihan_bill_id')->constrained('penagihan_bills')->cascadeOnDelete();
            $table->foreignId('anggota_id')->constrained('anggotas')->cascadeOnDelete();
            
            $table->decimal('simpanan_pokok', 15, 2)->default(0);
            $table->decimal('simpanan_wajib', 15, 2)->default(0);
            $table->decimal('simpanan_sukarela', 15, 2)->default(0);
            $table->decimal('jumlah_simpanan', 15, 2)->default(0);
            
            $table->decimal('jumlah_pinjaman', 15, 2)->default(0);
            
            $table->decimal('total_potongan', 15, 2)->default(0);
            $table->string('status')->default('Belum Lunas'); // Lunas / Belum Lunas
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penagihan_bill_details');
    }
};
