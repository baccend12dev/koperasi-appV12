<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create tagihan_pinjamans (batch tagihan per periode)
        Schema::create('tagihan_pinjamans', function (Blueprint $table) {
            $table->id();
            $table->string('periode');                // e.g. "April 2026"
            $table->date('tanggal_tagihan');
            $table->string('type')->default('Semua Anggota'); // Semua Anggota / By Checklist
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('status', ['Draft', 'Partial', 'Paid'])->default('Draft');
            $table->timestamps();
        });

        // 2. Drop & recreate pembayaran_angsurans (now stores actual payment transactions)
        Schema::dropIfExists('pembayaran_angsurans');
        Schema::create('pembayaran_angsurans', function (Blueprint $table) {
            $table->id();
            $table->string('type_bayar')->default('normal'); // normal / pelunasan
            $table->decimal('jumlah', 15, 2);
            $table->unsignedBigInteger('user_id');      // FK anggota
            $table->unsignedBigInteger('loan_id');       // FK pinjaman
            $table->unsignedBigInteger('angsuran_id')->nullable(); // FK pinjaman_angsurans (null for pelunasan)
            $table->date('tanggal_bayar');
            $table->timestamps();
        });

        // 3. Add tagihan_pinjaman_id to pinjaman_angsurans
        Schema::table('pinjaman_angsurans', function (Blueprint $table) {
            $table->unsignedBigInteger('tagihan_pinjaman_id')->nullable()->after('payment_id');
        });
    }

    public function down(): void
    {
        Schema::table('pinjaman_angsurans', function (Blueprint $table) {
            $table->dropColumn('tagihan_pinjaman_id');
        });

        Schema::dropIfExists('pembayaran_angsurans');
        // Recreate old structure
        Schema::create('pembayaran_angsurans', function (Blueprint $table) {
            $table->id();
            $table->string('periode');
            $table->date('tanggal_pembayaran');
            $table->string('type')->default('Semua Anggota');
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('status', ['Draft', 'Partial', 'Paid'])->default('Draft');
            $table->timestamps();
        });

        Schema::dropIfExists('tagihan_pinjamans');
    }
};
