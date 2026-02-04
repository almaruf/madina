<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'path',
        'url',
        'thumbnail_path',
        'thumbnail_url',
        'alt_text',
        'is_primary',
        'order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    protected $appends = [
        'signed_url',
        'signed_thumbnail_url',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Generate a signed URL for the image (valid for 1 hour)
     */
    public function getSignedUrlAttribute()
    {
        if (!$this->path) {
            return null;
        }

        try {
            return \Storage::disk('s3')->temporaryUrl(
                $this->path,
                now()->addHours(1)
            );
        } catch (\Exception $e) {
            \Log::error('Failed to generate signed URL', [
                'path' => $this->path,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Generate a signed URL for the thumbnail (valid for 1 hour)
     */
    public function getSignedThumbnailUrlAttribute()
    {
        if (!$this->thumbnail_path) {
            return null;
        }

        try {
            return \Storage::disk('s3')->temporaryUrl(
                $this->thumbnail_path,
                now()->addHours(1)
            );
        } catch (\Exception $e) {
            \Log::error('Failed to generate signed thumbnail URL', [
                'path' => $this->thumbnail_path,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
