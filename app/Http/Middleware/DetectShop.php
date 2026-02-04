<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\ShopContext;

class DetectShop
{
    public function handle(Request $request, Closure $next)
    {
        // Skip shop detection for auth API routes only
        if ($request->is('api/auth/*')) {
            return $next($request);
        }

        // For admin routes, allow inactive shops
        $isAdminRoute = $request->is('admin') || $request->is('admin/*') || $request->is('api/admin/*');

        // Get the host/domain
        $domain = $request->getHost();

        // Try to find shop by domain
        $shop = ShopContext::findByDomain($domain);

        // If not found by domain, try query parameter (for development)
        if (!$shop && $request->has('shop')) {
            $shop = ShopContext::findBySlug($request->query('shop'));
        }

        // If not found, try to get first shop (active or inactive based on route type)
        if (!$shop) {
            $shop = $isAdminRoute 
                ? \App\Models\Shop::first() 
                : \App\Models\Shop::active()->first();
        }

        // If still no shop found, abort (only for public shop routes)
        if (!$shop && !$isAdminRoute) {
            return response()->json(['message' => 'Shop not found'], 404);
        }

        // Set the current shop in context (even if null for admin routes)
        if ($shop) {
            ShopContext::setShop($shop);
        }

        return $next($request);
    }
}
