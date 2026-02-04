<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ShopContext;

class ShopController extends Controller
{
    /**
     * Get public shop configuration including VAT settings
     */
    public function config()
    {
        $shop = ShopContext::getShop();
        
        if (!$shop) {
            return response()->json(['message' => 'Shop not found'], 404);
        }

        return response()->json([
            'name' => $shop->name,
            'currency' => $shop->currency ?? 'GBP',
            'currency_symbol' => $shop->currency_symbol ?? '£',
            'delivery_fee' => $shop->delivery_fee ?? 0,
            'free_delivery_threshold' => $shop->free_delivery_threshold,
            'min_order_amount' => $shop->min_order_amount,
            'vat' => [
                'registered' => (bool) $shop->vat_registered,
                'rate' => $shop->vat_rate ?? 20.00,
                'prices_include_vat' => (bool) ($shop->prices_include_vat ?? true),
            ],
        ]);
    }
}
