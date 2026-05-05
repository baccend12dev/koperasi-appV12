<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function invoicePeriod()
    {
        return $this->belongsTo(InvoicePeriod::class);
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'user_id');
    }

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class, 'loan_id');
    }
}
