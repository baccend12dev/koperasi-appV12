<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagihanSimpanan extends Model
{
    protected $fillable = [
        'periode',
        'tanggal_generate',
        'type',
        'total',
        'status',
    ];

    public function details()
    {
        return $this->hasMany(TagihanSimpananDetail::class, 'tagihan_simpanan_id');
    }
}
