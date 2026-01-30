<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shop_id',
        'name',
        'slug',
        'description',
        'short_description',
        'type',
        'brand',
        'manufacturer',
        'country_of_origin',
        'ingredients',
        'allergen_info',
        'nutritional_info',
        'storage_instructions',
        'is_halal',
        'cut_type',
        'meat_type',
        'sku',
        'barcode',
        'is_active',
        'is_featured',
        'is_on_sale',
        'stock_quantity',
        'low_stock_threshold',
        'stock_status',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'order',
        'times_purchased',
    ];

    protected $casts = [
        'is_halal' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_on_sale' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOnSale($query)
    {
        return $query->where('is_on_sale', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_status', 'in_stock');
    }

    public function scopeMeat($query)
    {
        return $query->where('type', 'meat');
    }

    public function scopePopular($query, $limit = 15)
    {
        return $query->orderBy('times_purchased', 'desc')->limit($limit);
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->low_stock_threshold;
    }
}
