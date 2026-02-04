<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, SoftDeletes, HasApiTokens;

    protected $fillable = [
        'shop_id',
        'phone',
        'name',
        'email',
        'role',
        'is_active',
        'phone_verified',
        'phone_verified_at',
        'deletion_requested_at',
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected $casts = [
        'phone_verified' => 'boolean',
        'is_active' => 'boolean',
        'phone_verified_at' => 'datetime',
        'deletion_requested_at' => 'datetime',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    public function isShopAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin', 'owner', 'staff']);
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function canManageAdmin(): bool
    {
        return $this->isSuperAdmin();
    }

    public function canManageShop(): bool
    {
        return $this->isSuperAdmin();
    }
}
