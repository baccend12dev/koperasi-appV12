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

    public function penagihanBill()
    {
        return $this->belongsTo(PenagihanBill::class, 'penagihan_bill_id');
    }

    public function pembayaran()
    {
        return $this->belongsTo(PembayaranAngsuran::class, 'payment_id');
    }
}
