<?php

namespace App\Services;

use App\Models\Shop;

class ShopConfigService
{
    protected $shop;

    public function __construct()
    {
        $this->shop = ShopContext::getShop();
    }

    public function get($key = null, $default = null)
    {
        if (!$this->shop) {
            return $default;
        }

        if ($key === null) {
            return $this->shop->toArray();
        }

        return $this->shop->{$key} ?? $default;
    }

    public function name()
    {
        return $this->shop?->name ?? 'ABC Grocery Shop';
    }

    public function slug()
    {
        return $this->shop?->slug ?? 'abc-grocery';
    }

    public function description()
    {
        return $this->shop?->description ?? 'Fresh groceries delivered to your door';
    }

    public function tagline()
    {
        return $this->shop?->tagline ?? 'Quality products at affordable prices';
    }

    public function phone()
    {
        return $this->shop?->phone;
    }

    public function email()
    {
        return $this->shop?->email;
    }

    public function address()
    {
        return $this->shop?->address_line_1;
    }

    public function fullAddress()
    {
        return $this->shop?->fullAddress();
    }

    public function currency()
    {
        return $this->shop?->currency ?? 'GBP';
    }

    public function currencySymbol()
    {
        return $this->shop?->currency_symbol ?? '£';
    }

    public function primaryColor()
    {
        return $this->shop?->primary_color ?? '#10B981';
    }

    public function isFeatureEnabled($feature)
    {
        return $this->shop?->isFeatureEnabled($feature) ?? false;
    }

    public function hasHalalProducts()
    {
        return $this->shop?->has_halal_products ?? false;
    }

    public function hasOrganicProducts()
    {
        return $this->shop?->has_organic_products ?? false;
    }

    public function deliveryConfig()
    {
        if (!$this->shop) {
            return [
                'radius_km' => 5,
                'min_order_amount' => 10.00,
                'delivery_fee' => 2.99,
                'free_delivery_threshold' => 30.00,
                'currency' => 'GBP',
                'currency_symbol' => '£',
            ];
        }
        return [
            'radius_km' => $this->shop->delivery_radius_km,
            'min_order_amount' => $this->shop->min_order_amount,
            'delivery_fee' => $this->shop->delivery_fee,
            'free_delivery_threshold' => $this->shop->free_delivery_threshold,
            'currency' => $this->shop->currency,
            'currency_symbol' => $this->shop->currency_symbol,
        ];
    }

    public function operatingHours($day = null)
    {
        if (!$this->shop) {
            return null;
        }
        
        if ($day) {
            $field = strtolower($day) . '_hours';
            return $this->shop->{$field};
        }

        return [
            'monday' => $this->shop->monday_hours,
            'tuesday' => $this->shop->tuesday_hours,
            'wednesday' => $this->shop->wednesday_hours,
            'thursday' => $this->shop->thursday_hours,
            'friday' => $this->shop->friday_hours,
            'saturday' => $this->shop->saturday_hours,
            'sunday' => $this->shop->sunday_hours,
        ];
    }

    public function getShop(): ?Shop
    {
        return $this->shop;
    }
}

