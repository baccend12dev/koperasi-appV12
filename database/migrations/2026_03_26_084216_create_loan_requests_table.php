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
        Schema::create('loan_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Using user_id as requested
            $table->unsignedBigInteger('jenis_pinjaman_id')->nullable();
            $table->decimal('jumlah_pengajuan', 15, 2);
            $table->integer('tenor'); // in months
            $table->decimal('bunga', 5, 2)->default(0); // e.g., 2.5%
            $table->decimal('total_bunga', 15, 2)->default(0);
            $table->decimal('total_pinjaman', 15, 2);
            $table->decimal('cicilan_per_bulan', 15, 2);
            $table->text('keterangan')->nullable();
            
            // Status & Approval
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('created_by')->nullable(); // Admin input
            $table->unsignedBigInteger('approved_by')->nullable(); // Ketua
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('alasan_penolakan')->nullable();
            
            $table->timestamps();

            // Optionally add foreign keys if needed, assuming users and jenis_pinjaman exist
            // $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_requests');
    }
};
