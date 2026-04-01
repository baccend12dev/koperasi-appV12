<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranAngsuran extends Model
{
    protected $table = 'pembayaran_angsurans';
    protected $guarded = [];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'user_id');
    }

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class, 'loan_id');
    }

    public function angsuran()
    {
        return $this->belongsTo(PinjamanAngsuran::class, 'angsuran_id');
    }
}
