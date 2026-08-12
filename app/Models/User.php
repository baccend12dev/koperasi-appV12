<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nik',
        'name',
        'email',
        'phone',
        'password',
        'role_id',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user')->withPivot('access_type');
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permissionName): bool
    {
        // 1. Super Admin bypass (Full Access)
        if ($this->role && in_array(strtolower($this->role->name), ['super admin', 'superadmin'])) {
            return true;
        }

        // 2. Direct User Permissions (Override)
        $directPerm = $this->permissions()->where('name', $permissionName)->first();
        if ($directPerm) {
            return $directPerm->pivot->access_type === 'grant';
        }

        // 3. Role Permissions
        if ($this->role) {
            return $this->role->permissions()->where('name', $permissionName)->exists();
        }

        return false;
    }
}
