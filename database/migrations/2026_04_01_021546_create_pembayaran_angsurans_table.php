<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_angsurans', function (Blueprint $table) {
            $table->id();
            $table->string('periode');              // e.g. "April 2026"
            $table->date('tanggal_pembayaran');
            $table->string('type')->default('Semua Anggota'); // Semua Anggota / By Checklist
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('status', ['Draft', 'Partial', 'Paid'])->default('Draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_angsurans');
    }
};
