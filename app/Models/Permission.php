<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'label',
        'module',
        'description',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'permission_user')->withPivot('access_type');
    }
}
