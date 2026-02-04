<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shop_id',
        'name',
        'slug',
        'description',
        'path',
        'url',
        'thumbnail_path',
        'thumbnail_url',
        'parent_id',
        'order',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected $appends = ['signed_url', 'signed_thumbnail_url'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
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
            \Log::error('Failed to generate signed URL for category: ' . $e->getMessage());
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
            \Log::error('Failed to generate signed thumbnail URL for category: ' . $e->getMessage());
            return $this->thumbnail_url ?? $this->url;
        }
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
