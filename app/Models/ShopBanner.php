<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopBanner extends Model
{
    protected $fillable = [
        'shop_id',
        'title',
        'description',
        'image',
        'link',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}
