<?php

namespace App\Services;

use App\Models\Shop;
use Illuminate\Support\Facades\Cache;

class ShopContext
{
    protected static $currentShop = null;

    /**
     * Set the current shop
     */
    public static function setShop(Shop $shop): void
    {
        static::$currentShop = $shop;
    }

    /**
     * Get the current shop
     */
    public static function getShop(): ?Shop
    {
        return static::$currentShop;
    }

    /**
     * Get shop ID for current request
     */
    public static function getShopId(): ?int
    {
        return static::$currentShop?->id;
    }

    /**
     * Check if a shop is set
     */
    public static function hasShop(): bool
    {
        return static::$currentShop !== null;
    }

    /**
     * Get shop by domain
     */
    public static function findByDomain(string $domain): ?Shop
    {
        // Remove www. prefix if present
        $domain = str_replace('www.', '', strtolower($domain));

        // Try to find shop by domain
        $shop = Cache::remember("shop.domain.{$domain}", 3600, function () use ($domain) {
            return Shop::where('domain', $domain)->active()->first();
        });

        return $shop;
    }

    /**
     * Get shop by slug
     */
    public static function findBySlug(string $slug): ?Shop
    {
        return Cache::remember("shop.slug.{$slug}", 3600, function () use ($slug) {
            return Shop::where('slug', $slug)->active()->first();
        });
    }

    /**
     * Get shop by ID
     */
    public static function findById(int $id): ?Shop
    {
        return Cache::remember("shop.id.{$id}", 3600, function () use ($id) {
            return Shop::where('id', $id)->active()->first();
        });
    }

    /**
     * Clear shop cache
     */
    public static function clearCache(): void
    {
        Cache::flush();
    }
}
