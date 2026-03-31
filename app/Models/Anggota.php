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

    /** Relasi ke Master Simpanan */
    public function masterSimpanan()
    {
        return $this->hasOne(MasterSimpanan::class, 'anggota_id');
    }

    public function transaksiSimpanan()
    {
        return $this->hasMany(TransaksiSimpanan::class, 'anggota_id');
    }

    public function pinjaman()
    {
        return $this->hasMany(Pinjaman::class, 'user_id');
    }

    public function pinjamanAktif()
    {
        return $this->hasMany(Pinjaman::class, 'user_id')->where('status', 'berjalan');
    }

    public function pinjamanPending()
    {
        return $this->hasMany(Pinjaman::class, 'user_id')->where('status', 'pending');
    }

    public function pinjamanApproved()
    {
        return $this->hasMany(Pinjaman::class, 'user_id')->where('status', 'approved');
    }

    public function pinjamanRejected()
    {
        return $this->hasMany(Pinjaman::class, 'user_id')->where('status', 'rejected');
    }

    public function pinjamanLunas()
    {
        return $this->hasMany(Pinjaman::class, 'user_id')->where('status', 'lunas');
    }
}
