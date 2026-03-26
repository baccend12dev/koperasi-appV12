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
        Schema::create('master_jenis_pinjaman', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pinjaman', 100);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->decimal('limit_maksimal', 15, 2)->nullable();
            $table->decimal('bunga', 5, 2)->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();

            $table->foreign('parent_id')
                  ->references('id')->on('master_jenis_pinjaman')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_jenis_pinjamen');
    }
};
