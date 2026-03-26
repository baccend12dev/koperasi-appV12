<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    protected $table = 'pinjamans';
    protected $guarded = [];

    public function loanRequest()
    {
        return $this->belongsTo(LoanRequest::class, 'loan_request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function angsuran()
    {
        return $this->hasMany(PinjamanAngsuran::class, 'loan_id');
    }
}
