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
        Schema::create('invoice_periods', function (Blueprint $table) {
            $table->id();
            $table->string('periode', 7); // e.g. 2026-04
            $table->decimal('total_gaji', 15, 2)->default(0);
            $table->decimal('total_mandiri', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->enum('status', ['draft', 'generated', 'closed'])->default('draft');
            $table->timestamp('generated_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('invoice_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_period_id')->constrained('invoice_periods')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('loan_id');
            $table->enum('payment_method', ['gaji', 'mandiri'])->default('gaji');
            $table->string('jenis_pinjaman');
            $table->integer('cicilan_ke');
            $table->integer('tenor');
            $table->decimal('cicilan_amount', 15, 2);
            $table->decimal('sisa_pinjaman', 15, 2);
            $table->integer('sisa_tenor');
            $table->enum('status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('anggotas')->onDelete('cascade');
            $table->foreign('loan_id')->references('id')->on('pinjamans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_details');
        Schema::dropIfExists('invoice_periods');
    }
};
