<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\ShopContext;

class DetectShop
{
    public function handle(Request $request, Closure $next)
    {
        // Skip shop detection for admin routes
        if ($request->is('admin') || $request->is('admin/*')) {
            return $next($request);
        }

        // Get the host/domain
        $domain = $request->getHost();

        // Try to find shop by domain
        $shop = ShopContext::findByDomain($domain);

        // If not found by domain, try query parameter (for development)
        if (!$shop && $request->has('shop')) {
            $shop = ShopContext::findBySlug($request->query('shop'));
        }

        // If not found, try to get first active shop
        if (!$shop) {
            $shop = \App\Models\Shop::active()->first();
        }

        // If still no shop found, abort (only for public shop routes)
        if (!$shop) {
            return response()->json(['message' => 'Shop not found'], 404);
        }

        // Set the current shop in context
        ShopContext::setShop($shop);

        return $next($request);
    }
}
