<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ShopBanner extends Model
{
    protected $fillable = [
        'shop_id',
        'title',
        'description',
        'path',
        'url',
        'thumbnail_path',
        'thumbnail_url',
        'link',
        'order',
        'is_primary',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_primary' => 'boolean',
    ];

    protected $appends = ['signed_url', 'signed_thumbnail_url'];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    public function getSignedUrlAttribute()
    {
        if (!$this->path) {
            return null;
        }

        try {
            return Storage::disk('s3')->temporaryUrl(
                $this->path,
                now()->addHour()
            );
        } catch (\Exception $e) {
            \Log::error('Failed to generate signed URL for banner: ' . $e->getMessage());
            return $this->url;
        }
    }

    public function getSignedThumbnailUrlAttribute()
    {
        if (!$this->thumbnail_path) {
            return $this->getSignedUrlAttribute();
        }

        try {
            return Storage::disk('s3')->temporaryUrl(
                $this->thumbnail_path,
                now()->addHour()
            );
        } catch (\Exception $e) {
            \Log::error('Failed to generate signed thumbnail URL for banner: ' . $e->getMessage());
            return $this->thumbnail_url ?? $this->url;
        }
    }
}
