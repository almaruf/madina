<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ShopAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || !$request->user()->isShopAdmin()) {
            return response()->json([
                'message' => 'Unauthorized. Shop admin access required.'
            ], 403);
        }

        return $next($request);
    }
}
