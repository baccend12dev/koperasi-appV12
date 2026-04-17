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
        Schema::create('pengambilan_simpanan_settlements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pengambilan_simpanan_id');
            $table->unsignedBigInteger('pinjaman_id');
            $table->timestamps();

            $table->foreign('pengambilan_simpanan_id', 'ps_settlement_ps_id_foreign')->references('id')->on('pengambilan_simpanans')->onDelete('cascade');
            $table->foreign('pinjaman_id', 'ps_settlement_pinjaman_id_foreign')->references('id')->on('pinjamans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengambilan_simpanan_settlements');
    }
};
