<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pencairan extends Model
{
    protected $table = 'pencairans';

    protected $fillable = [
        'ref_type',
        'ref_id',
        'anggota_id',
        'nominal',
        'tanggal',
        'status',
        'metode',
        'bukti_transfer',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'tanggal'  => 'date',
        'nominal'  => 'decimal:2',
    ];

    /** Relasi ke Anggota */
    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }

    /**
     * Polymorphic-style: ambil record sumber secara dinamis.
     * ref_type = 'pinjaman'  → LoanRequest
     * ref_type = 'simpanan'  → PengambilanSimpanan
     */
    public function referensi()
    {
        if ($this->ref_type === 'pinjaman') {
            return $this->belongsTo(LoanRequest::class, 'ref_id');
        }
        return $this->belongsTo(PengambilanSimpanan::class, 'ref_id');
    }

    /** Scope helpers */
    public function scopePending($query)   { return $query->where('status', 'pending'); }
    public function scopePaid($query)      { return $query->where('status', 'paid'); }
    public function scopeFailed($query)    { return $query->where('status', 'failed'); }
    public function scopePinjaman($query)  { return $query->where('ref_type', 'pinjaman'); }
    public function scopeSimpanan($query)  { return $query->where('ref_type', 'simpanan'); }
}
