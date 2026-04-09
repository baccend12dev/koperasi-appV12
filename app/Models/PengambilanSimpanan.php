<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengambilanSimpanan extends Model
{
    protected $guarded = [];
    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
