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
        Schema::create('penagihan_bills', function (Blueprint $table) {
            $table->id();
            $table->string('periode');
            $table->date('tgl_generate');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('keterangan')->nullable();
            $table->enum('status', ['Draft', 'Partial', 'Paid'])->default('Draft');
            $table->string('type')->default('Semua Anggota');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penagihan_bills');
    }
};
