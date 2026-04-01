<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagihanPinjaman extends Model
{
    protected $table = 'tagihan_pinjamans';
    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(PinjamanAngsuran::class, 'tagihan_pinjaman_id');
    }
}
