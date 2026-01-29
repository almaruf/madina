<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CustomerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || !$request->user()->isCustomer()) {
            return response()->json([
                'message' => 'Unauthorized. Customer access required.'
            ], 403);
        }

        return $next($request);
    }
}
