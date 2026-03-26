<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanRequest extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pinjaman()
    {
        return $this->hasOne(Pinjaman::class, 'loan_request_id');
    }
}
