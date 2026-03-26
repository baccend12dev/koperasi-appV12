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
        Schema::create('pinjamans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_request_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('jenis_pinjaman_id')->nullable();
            $table->decimal('jumlah_pinjaman', 15, 2);
            $table->integer('tenor');
            $table->decimal('bunga', 5, 2)->default(0);
            $table->decimal('total_bunga', 15, 2)->default(0);
            $table->decimal('total_pinjaman', 15, 2);
            $table->decimal('cicilan_per_bulan', 15, 2);
            
            // Tracking
            $table->decimal('sisa_pinjaman', 15, 2);
            $table->integer('sisa_tenor');
            $table->decimal('total_terbayar', 15, 2)->default(0);
            
            // Status
            $table->enum('status', ['berjalan', 'lunas'])->default('berjalan');
            
            // Tanggal
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinjamen');
    }
};
