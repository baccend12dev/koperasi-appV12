<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenagihanBill extends Model
{
    use HasFactory;

    protected $table = 'penagihan_bills';
    protected $guarded = [];

    // Semua detail digabung ke penagihan_bill_details
    public function details()
    {
        return $this->hasMany(PenagihanBillDetail::class, 'penagihan_bill_id');
    }
}
