<?php
// database/migrations/2024_01_01_000002_create_anggotas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggotas', function (Blueprint $table) {
            $table->id();
            $table->string('no_pegawai');
            $table->string('nik')->nullable();
            $table->string('nama_anggota');
            $table->integer('department_id');
            $table->integer('bagian_id');
            $table->text('alamat')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('ket_bagian')->nullable();
            $table->date('join_date');
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggotas');
    }
};
