<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $user = $request->user();
        $shopId = null;

        if ($user && $user->isAdmin()) {
            $requestedShopId = $request->query('shop_id');
            if ($requestedShopId && $requestedShopId !== 'all') {
                $shopId = (int) $requestedShopId;
            }
        } else {
            $shopId = \App\Services\ShopContext::getShopId();
        }

        // Total orders
        $totalOrders = Order::when($shopId, function ($query) use ($shopId) {
            $query->where('shop_id', $shopId);
        })->count();

        // Pending orders
        $pendingOrders = Order::when($shopId, function ($query) use ($shopId) {
            $query->where('shop_id', $shopId);
        })->where('status', 'pending')->count();

        // Today's orders
        $todayOrders = Order::when($shopId, function ($query) use ($shopId) {
            $query->where('shop_id', $shopId);
        })->whereDate('created_at', today())->count();

        // Total revenue (completed orders only)
        $totalRevenue = Order::when($shopId, function ($query) use ($shopId) {
            $query->where('shop_id', $shopId);
        })->whereIn('status', ['completed', 'delivered'])
            ->where('payment_status', 'paid')
            ->sum('total');

        // Recent orders (last 10)
        $recentOrders = Order::when($shopId, function ($query) use ($shopId) {
            $query->where('shop_id', $shopId);
        })->with(['user', 'items', 'shop'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Additional stats
        $totalProducts = Product::when($shopId, function ($query) use ($shopId) {
            $query->where('shop_id', $shopId);
        })->count();

        // Count customers who have placed orders
        $totalCustomers = User::where('role', 'customer')
            ->whereHas('orders', function ($query) use ($shopId) {
                if ($shopId) {
                    $query->where('shop_id', $shopId);
                }
            })
            ->count();

        return response()->json([
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'today_orders' => $todayOrders,
            'total_revenue' => number_format($totalRevenue, 2),
            'total_products' => $totalProducts,
            'total_customers' => $totalCustomers,
            'recent_orders' => $recentOrders,
        ]);
    }
}
