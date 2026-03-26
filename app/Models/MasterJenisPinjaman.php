<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterJenisPinjaman extends Model
{
    protected $table = 'master_jenis_pinjaman';
    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo(MasterJenisPinjaman::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MasterJenisPinjaman::class, 'parent_id');
    }
}
