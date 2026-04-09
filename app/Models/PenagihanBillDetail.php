<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class PenagihanBillDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penagihan_bill_details';
    protected $guarded = [];

    public function penagihanBill()
    {
        return $this->belongsTo(PenagihanBill::class, 'penagihan_bill_id');
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }
}
