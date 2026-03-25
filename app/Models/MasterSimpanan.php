<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSimpanan extends Model
{
    protected $fillable = [
        'anggota_id',
        'simpanan_pokok',
        'simpanan_wajib',
        'simpanan_sukarela',
        'tanggal_mulai',
        'tanggal_akhir',
        'aktif',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }
}
