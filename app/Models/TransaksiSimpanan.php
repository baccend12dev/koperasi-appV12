<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiSimpanan extends Model
{
    protected $guarded = [];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    public function jenisSimpanan()
    {
        return $this->belongsTo(JenisSimpanan::class);
    }
}
