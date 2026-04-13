<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pencairans', function (Blueprint $table) {
            $table->id();

            // Polymorphic reference: pinjaman / simpanan (pengambilan)
            $table->enum('ref_type', ['pinjaman', 'simpanan']);
            $table->unsignedBigInteger('ref_id');  // id loan_request (pinjaman) atau id pengambilan_simpanan

            $table->unsignedBigInteger('anggota_id');
            $table->decimal('nominal', 15, 2);
            $table->date('tanggal');

            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->enum('metode', ['transfer', 'cash'])->default('transfer');
            $table->string('bukti_transfer')->nullable(); // path file bukti

            $table->text('keterangan')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            $table->foreign('anggota_id')->references('id')->on('anggotas')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pencairans');
    }
};
