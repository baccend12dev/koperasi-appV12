<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanRequestTopup extends Model
{
    protected $fillable = ['loan_request_id', 'pinjaman_id'];

    public function loanRequest()
    {
        return $this->belongsTo(LoanRequest::class);
    }

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class);
    }
}
