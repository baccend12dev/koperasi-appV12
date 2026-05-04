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

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'user_id');
    }

    public function angsuran()
    {
        return $this->hasMany(PinjamanAngsuran::class, 'loan_id');
    }

    public function jenisPinjaman()
    {
        return $this->belongsTo(MasterJenisPinjaman::class, 'jenis_pinjaman_id');
    }

}
