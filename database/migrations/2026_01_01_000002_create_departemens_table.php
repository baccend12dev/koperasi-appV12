<?php
// database/migrations/2026_01_01_000001_create_departments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departemens', function (Blueprint $table) {
            $table->id();                                        // ID - BIGINT AUTO INCREMENT
            $table->string('nama');  
            $table->string('kode')->nullable();
            $table->text('deskripsi')->nullable();                     // KET - TEXT (nullable)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departemens');
    }
};
