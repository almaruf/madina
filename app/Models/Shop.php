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
        'monday_open',
        'monday_close',
        'monday_closed',
        'tuesday_open',
        'tuesday_close',
        'tuesday_closed',
        'wednesday_open',
        'wednesday_close',
        'wednesday_closed',
        'thursday_open',
        'thursday_close',
        'thursday_closed',
        'friday_open',
        'friday_close',
        'friday_closed',
        'saturday_open',
        'saturday_close',
        'saturday_closed',
        'sunday_open',
        'sunday_close',
        'sunday_closed',
        'domain',
        'is_active',
    ];

    protected $casts = [
        'vat_registered' => 'boolean',
        'prices_include_vat' => 'boolean',
        'is_active' => 'boolean',
        'has_halal_products' => 'boolean',
        'has_organic_products' => 'boolean',
        'has_international_products' => 'boolean',
        'delivery_enabled' => 'boolean',
        'collection_enabled' => 'boolean',
        'online_payment' => 'boolean',
        'loyalty_program' => 'boolean',
        'reviews_enabled' => 'boolean',
        'monday_closed' => 'boolean',
        'tuesday_closed' => 'boolean',
        'wednesday_closed' => 'boolean',
        'thursday_closed' => 'boolean',
        'friday_closed' => 'boolean',
        'saturday_closed' => 'boolean',
        'sunday_closed' => 'boolean',
        'min_order_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'free_delivery_threshold' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'delivery_radius_km' => 'decimal:2',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Retrieve the model for a bound value.
     * Include trashed models for admin routes.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? $this->getRouteKeyName(), $value)
            ->withTrashed()
            ->firstOrFail();
    }

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

    /**
     * Check if shop is currently open
     */
    public function isCurrentlyOpen(): bool
    {
        $now = now();
        $dayName = strtolower($now->format('l')); // monday, tuesday, etc.
        
        $closedField = "{$dayName}_closed";
        if ($this->$closedField) {
            return false;
        }
        
        $openField = "{$dayName}_open";
        $closeField = "{$dayName}_close";
        
        if (!$this->$openField || !$this->$closeField) {
            return false;
        }
        
        $currentTime = $now->format('H:i:s');
        return $currentTime >= $this->$openField && $currentTime <= $this->$closeField;
    }

    /**
     * Get formatted operating hours for a specific day
     */
    public function getFormattedHours(string $day): string
    {
        $closedField = "{$day}_closed";
        if ($this->$closedField) {
            return 'Closed';
        }
        
        $openField = "{$day}_open";
        $closeField = "{$day}_close";
        
        if (!$this->$openField || !$this->$closeField) {
            return 'Not set';
        }
        
        return $this->formatTime($this->$openField) . ' - ' . $this->formatTime($this->$closeField);
    }

    /**
     * Format time from 24-hour to 12-hour with AM/PM
     */
    private function formatTime(string $time): string
    {
        $parts = explode(':', $time);
        $hour = (int)$parts[0];
        $minute = $parts[1];
        
        $period = $hour >= 12 ? 'PM' : 'AM';
        $hour = $hour > 12 ? $hour - 12 : ($hour == 0 ? 12 : $hour);
        
        return sprintf('%d:%s %s', $hour, $minute, $period);
    }
}
