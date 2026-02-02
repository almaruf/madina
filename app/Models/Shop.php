<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'legal_company_name',
        'company_registration_number',
        'slug',
        'description',
        'tagline',
        'address_line_1',
        'address_line_2',
        'city',
        'postcode',
        'country',
        'phone',
        'email',
        'support_email',
        'business_type',
        'specialization',
        'has_halal_products',
        'has_organic_products',
        'has_international_products',
        'delivery_enabled',
        'collection_enabled',
        'online_payment',
        'loyalty_program',
        'reviews_enabled',
        'delivery_radius_km',
        'min_order_amount',
        'delivery_fee',
        'free_delivery_threshold',
        'currency',
        'currency_symbol',
        'vat_registered',
        'vat_number',
        'vat_rate',
        'prices_include_vat',
        'primary_color',
        'secondary_color',
        'logo_url',
        'favicon_url',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'whatsapp_number',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'bank_sort_code',
        'bank_iban',
        'bank_swift_code',
        'monday_hours',
        'tuesday_hours',
        'wednesday_hours',
        'thursday_hours',
        'friday_hours',
        'saturday_hours',
        'sunday_hours',
        'domain',
        'vat_registered' => 'boolean',
        'prices_include_vat' => 'boolean',
        'is_active' => 'boolean',
        'min_order_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'free_delivery_threshold' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'has_halal_products' => 'boolean',
        'has_organic_products' => 'boolean',
        'has_international_products' => 'boolean',
        'delivery_enabled' => 'boolean',
        'collection_enabled' => 'boolean',
        'online_payment' => 'boolean',
        'loyalty_program' => 'boolean',
        'reviews_enabled' => 'boolean',
        'is_active' => 'boolean',
        'min_order_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'free_delivery_threshold' => 'decimal:2',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function deliverySlots()
    {
        return $this->hasMany(DeliverySlot::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function fullAddress(): string
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->postcode,
        ]);
        return implode(', ', $parts);
    }

    public function isFeatureEnabled($feature): bool
    {
        return (bool) $this->{"{$feature}_enabled"} ?? false;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDomain($query, $domain)
    {
        return $query->where('domain', $domain);
    }

    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
