<?php
// app/Models/Anggota.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_pegawai',
        'nik',
        'nama_anggota',
        'department_id',
        'bagian_id',
        'alamat',
        'no_hp',
        'jenis_kelamin',
        'tgl_lahir',
        'jabatan',
        'ket_bagian',
        'tgl_bergabung',
        'ikatan_kerja',
        'status_anggota',
    ];

    /** Relasi ke departemen */
    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'department_id');
    }

    /** Relasi ke manajer (self-referential) */
    public function manajer()
    {
        return $this->belongsTo(Anggota::class, 'manajer_id');
    }

    /** Anggota yang dikelola oleh anggota ini */
    public function bawahannya()
    {
        return $this->hasMany(Anggota::class, 'manajer_id');
    }

    /** Relasi ke Master Simpanan */
    public function masterSimpanan()
    {
        return $this->hasOne(MasterSimpanan::class, 'anggota_id');
    }
}
