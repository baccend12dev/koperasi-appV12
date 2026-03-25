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
        Schema::create('tagihan_simpanans', function (Blueprint $table) {
            $table->id();
            $table->string('periode');
            $table->date('tanggal_generate');
            $table->string('type')->default('Semua Anggota');
            $table->decimal('total', 15, 2)->default(0);
            $table->string('status')->default('Draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan_simpanans');
    }
};
