<?php
// app/Models/Departemen.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'kode', 'deskripsi'];

    /** Anggota di departemen ini */
    public function anggota()
    {
        return $this->hasMany(Anggota::class, 'department_id');
    }
}
