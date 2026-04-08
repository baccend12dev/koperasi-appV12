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
            $table->id();                                          // ID - INT
            $table->text('nik')->nullable();                       // NIK - TEXT
            $table->text('no_ktp')->nullable();                    // NO KTP - TEXT
            $table->integer('department_id')->nullable();          // DEPARTMENT_ID - INT
            $table->text('bagian')->nullable();                    // BAGIAN - TEXT
            $table->text('ket_bagian')->nullable();                  // KETERBAG - TEXT
            $table->text('no_hp')->nullable();                     // NO_HP - TEXT
            $table->text('nama_anggota')->nullable();                      // NAMA - TEXT
            $table->integer('tanggungan')->nullable();             // TANGGUNGAN - INT
            $table->text('sex')->nullable();                       // SEX - TEXT
            $table->text('jabatan')->nullable();                   // JABATAN - TEXT
            $table->text('status_anggota')->nullable();                    // STATUS - TEXT
            $table->text('ikatan_kerja')->nullable();              // IKATAN_KERJA - TEXT
            $table->text('alamat')->nullable();                    // ALAMAT - TEXT
            $table->date('tgl_msk')->nullable();                   // TGL_MSK - DATE
            $table->date('tgl_lahir')->nullable();                 // TGL_LAHIR - DATE
            $table->text('kode')->nullable();                      // KODE - TEXT
            $table->text('foto')->nullable();                      // FOTO - TEXT
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggotas');
    }
};
