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
        Schema::create('pinjaman_angsurans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_id');
            $table->integer('angsuran_ke');
            $table->date('tanggal_jatuh_tempo');
            $table->decimal('jumlah_tagihan', 15, 2);
            $table->decimal('jumlah_dibayar', 15, 2)->default(0);
            $table->date('tanggal_bayar')->nullable();
            
            // Status
            $table->enum('status', ['belum_bayar', 'sudah_bayar'])->default('belum_bayar');
            
            $table->timestamps();
            
            // Optionally: $table->foreign('loan_id')->references('id')->on('pinjamans');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinjaman_angsurans');
    }
};
