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
        $shopId = \App\Services\ShopContext::getShopId();

        // Total orders
        $totalOrders = Order::where('shop_id', $shopId)->count();

        // Pending orders
        $pendingOrders = Order::where('shop_id', $shopId)
            ->where('status', 'pending')
            ->count();

        // Today's orders
        $todayOrders = Order::where('shop_id', $shopId)
            ->whereDate('created_at', today())
            ->count();

        // Total revenue (completed orders only)
        $totalRevenue = Order::where('shop_id', $shopId)
            ->whereIn('status', ['completed', 'delivered'])
            ->where('payment_status', 'paid')
            ->sum('total');

        // Recent orders (last 10)
        $recentOrders = Order::where('shop_id', $shopId)
            ->with(['user', 'items'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Additional stats
        $totalProducts = Product::where('shop_id', $shopId)->count();
        // Count customers who have placed orders at this shop
        $totalCustomers = User::where('role', 'customer')
            ->whereHas('orders', function($query) use ($shopId) {
                $query->where('shop_id', $shopId);
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
