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
        Schema::table('tagihan_simpanan_details', function (Blueprint $table) {
            $table->dropForeign(['tagihan_simpanan_id']);
            $table->dropColumn('tagihan_simpanan_id');
            $table->foreignId('penagihan_bill_id')->nullable()->constrained('penagihan_bills')->nullOnDelete();
        });

        Schema::table('pinjaman_angsurans', function (Blueprint $table) {
            if (Schema::hasColumn('pinjaman_angsurans', 'tagihan_pinjaman_id')) {
                $table->dropColumn('tagihan_pinjaman_id');
            }
            $table->foreignId('penagihan_bill_id')->nullable()->constrained('penagihan_bills')->nullOnDelete();
        });

        Schema::dropIfExists('tagihan_simpanans');
        Schema::dropIfExists('tagihan_pinjamans');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('tagihan_simpanans', function (Blueprint $table) {
            $table->id();
            $table->string('periode');
            $table->date('tanggal_generate');
            $table->string('type')->default('Semua Anggota');
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('status', ['Draft', 'Partial', 'Paid'])->default('Draft');
            $table->timestamps();
        });

        Schema::create('tagihan_pinjamans', function (Blueprint $table) {
            $table->id();
            $table->string('periode');
            $table->date('tanggal_tagihan');
            $table->string('type')->default('Semua Anggota');
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('status', ['Draft', 'Partial', 'Paid'])->default('Draft');
            $table->timestamps();
        });

        Schema::table('tagihan_simpanan_details', function (Blueprint $table) {
            $table->dropForeign(['penagihan_bill_id']);
            $table->dropColumn('penagihan_bill_id');
            $table->foreignId('tagihan_simpanan_id')->nullable()->constrained('tagihan_simpanans')->cascadeOnDelete();
        });

        Schema::table('pinjaman_angsurans', function (Blueprint $table) {
            $table->dropForeign(['penagihan_bill_id']);
            $table->dropColumn('penagihan_bill_id');
            $table->unsignedBigInteger('tagihan_pinjaman_id')->nullable();
        });
    }
};
