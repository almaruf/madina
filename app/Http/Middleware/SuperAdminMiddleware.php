<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'super_admin') {
            return response()->json([
                'message' => 'Unauthorized. Super admin access required.'
            ], 403);
        }

        return $next($request);
    }
}
