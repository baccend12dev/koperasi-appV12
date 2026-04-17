<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengambilanSimpananSettlement extends Model
{
    protected $guarded = [];

    public function pengambilan()
    {
        return $this->belongsTo(PengambilanSimpanan::class, 'pengambilan_simpanan_id');
    }

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class, 'pinjaman_id');
    }
}
