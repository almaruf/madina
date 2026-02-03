<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(Request $request)
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
        
        // Start with the base Order query
        $query = Order::query();
        
        // Handle archived filter first (must be before where clauses)
        if ($request->has('archived') && $request->archived == '1') {
            $query->onlyTrashed();
        }
        
        // Now apply shop_id filter and other conditions
        $query->when($shopId, function ($q) use ($shopId) {
            $q->where('shop_id', $shopId);
        })->with(['user', 'items.product', 'deliverySlot', 'address', 'shop']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('fulfillment_type')) {
            $query->where('fulfillment_type', $request->fulfillment_type);
        }

        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->latest()->paginate(50);

        return response()->json($orders);
    }

    public function show($id)
    {
        $order = Order::withTrashed()->with(['user', 'items.product', 'deliverySlot', 'address'])->findOrFail($id);

        return response()->json($order);
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,processing,ready,out_for_delivery,delivered,completed,cancelled,refunded',
            'admin_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $updates = ['status' => $request->status];

        if ($request->has('admin_notes')) {
            $updates['admin_notes'] = $request->admin_notes;
        }

        if ($request->status === 'confirmed' && !$order->confirmed_at) {
            $updates['confirmed_at'] = now();
        }

        if ($request->status === 'delivered' && !$order->delivered_at) {
            $updates['delivered_at'] = now();
        }

        if ($request->status === 'cancelled' && !$order->cancelled_at) {
            $updates['cancelled_at'] = now();
        }

        $order->update($updates);

        // Dispatch email notification when order is confirmed
        if ($request->status === 'confirmed') {
            \App\Jobs\SendOrderConfirmationEmail::dispatch($order);
        }

        return response()->json($order);
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $order->update(['payment_status' => $request->payment_status]);

        return response()->json($order);
    }

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
        $stats = [
            'total_orders' => Order::when($shopId, function ($query) use ($shopId) {
                $query->where('shop_id', $shopId);
            })->count(),
            'pending_orders' => Order::when($shopId, function ($query) use ($shopId) {
                $query->where('shop_id', $shopId);
            })->where('status', 'pending')->count(),
            'today_orders' => Order::when($shopId, function ($query) use ($shopId) {
                $query->where('shop_id', $shopId);
            })->whereDate('created_at', today())->count(),
            'total_revenue' => Order::when($shopId, function ($query) use ($shopId) {
                $query->where('shop_id', $shopId);
            })->whereIn('status', ['delivered', 'completed'])->sum('total'),
            'today_revenue' => Order::when($shopId, function ($query) use ($shopId) {
                $query->where('shop_id', $shopId);
            })->whereDate('created_at', today())->sum('total'),
        ];

        return response()->json($stats);
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(['message' => 'Order archived successfully']);
    }

    public function restore($id)
    {
        $order = Order::onlyTrashed()->findOrFail($id);
        $order->restore();

        return response()->json(['message' => 'Order restored successfully']);
    }

    public function forceDelete($id)
    {
        $order = Order::withTrashed()->findOrFail($id);
        $order->forceDelete();

        return response()->json(['message' => 'Order permanently deleted']);
    }
}
