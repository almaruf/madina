<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'phone',
        'otp',
        'expires_at',
        'attempts',
        'verified',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified' => 'boolean',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function isValid(): bool
    {
        return !$this->verified && !$this->isExpired() && $this->attempts < config('services.otp.max_attempts');
    }
}
