<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagihanSimpananDetail extends Model
{
    protected $fillable = [
        'tagihan_simpanan_id',
        'anggota_id',
        'simpanan_pokok',
        'simpanan_wajib',
        'simpanan_sukarela',
        'total',
        'status',
    ];

    public function tagihanSimpanan()
    {
        return $this->belongsTo(TagihanSimpanan::class);
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }
}
