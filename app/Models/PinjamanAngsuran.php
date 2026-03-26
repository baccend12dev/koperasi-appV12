<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinjamanAngsuran extends Model
{
    protected $table = 'pinjaman_angsurans';
    protected $guarded = [];

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class, 'loan_id');
    }
}
