<?php
// app/Models/Anggota.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'nokop',
        'name',
        'department_id',
        'join_date',
        'status',
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
}
