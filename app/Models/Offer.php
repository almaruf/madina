<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Offer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shop_id',
        'name',
        'slug',
        'description',
        'type',
        'discount_value',
        'buy_quantity',
        'get_quantity',
        'get_discount_percentage',
        'bundle_price',
        'min_purchase_amount',
        'max_uses_per_customer',
        'total_usage_limit',
        'current_usage_count',
        'starts_at',
        'ends_at',
        'badge_text',
        'badge_color',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'get_discount_percentage' => 'decimal:2',
        'bundle_price' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'offer_product')
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        $now = Carbon::now();
        return $query->where(function ($q) use ($now) {
            $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
        });
    }

    public function scopeForShop($query, $shopId)
    {
        return $query->where('shop_id', $shopId);
    }

    // Helpers
    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->ends_at && $this->ends_at->isPast()) {
            return false;
        }

        if ($this->total_usage_limit && $this->current_usage_count >= $this->total_usage_limit) {
            return false;
        }

        return true;
    }

    public function calculateDiscount($originalPrice, $quantity = 1)
    {
        if (!$this->isValid()) {
            return 0;
        }

        switch ($this->type) {
            case 'percentage_discount':
                return $originalPrice * ($this->discount_value / 100) * $quantity;

            case 'fixed_discount':
                return min($this->discount_value * $quantity, $originalPrice * $quantity);

            case 'bxgy_free':
                if ($quantity >= ($this->buy_quantity + $this->get_quantity)) {
                    $sets = floor($quantity / ($this->buy_quantity + $this->get_quantity));
                    return $sets * $this->get_quantity * $originalPrice;
                }
                return 0;

            case 'multibuy':
                if ($quantity >= $this->buy_quantity) {
                    $normalPrice = $quantity * $originalPrice;
                    $offerPrice = $this->bundle_price * floor($quantity / $this->buy_quantity);
                    $remainder = ($quantity % $this->buy_quantity) * $originalPrice;
                    return $normalPrice - ($offerPrice + $remainder);
                }
                return 0;

            case 'bxgy_discount':
                if ($quantity >= ($this->buy_quantity + $this->get_quantity)) {
                    $sets = floor($quantity / ($this->buy_quantity + $this->get_quantity));
                    $discountAmount = $originalPrice * ($this->get_discount_percentage / 100);
                    return $sets * $this->get_quantity * $discountAmount;
                }
                return 0;

            case 'flash_sale':
                if ($this->discount_value) {
                    return $originalPrice * ($this->discount_value / 100) * $quantity;
                }
                return 0;

            default:
                return 0;
        }
    }

    public function getDiscountedPrice($originalPrice, $quantity = 1)
    {
        $discount = $this->calculateDiscount($originalPrice, $quantity);
        return max(0, ($originalPrice * $quantity) - $discount);
    }

    public function incrementUsage()
    {
        $this->increment('current_usage_count');
    }
}
