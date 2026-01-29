<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\DeliverySlot;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $shopId = \App\Services\ShopContext::getShopId();
        $orders = Order::where('shop_id', $shopId)
            ->where('user_id', $request->user()->id)
            ->with(['items.product', 'deliverySlot', 'address'])
            ->latest()
            ->paginate(10);

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_variation_id' => 'required|exists:product_variations,id',
            'items.*.quantity' => 'required|integer|min:1',
            'fulfillment_type' => 'required|in:delivery,collection',
            'delivery_slot_id' => 'required|exists:delivery_slots,id',
            'address_id' => 'required_if:fulfillment_type,delivery|exists:addresses,id',
            'payment_method' => 'required|in:cash,card,online',
            'customer_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            // Calculate totals
            $subtotal = 0;
            $orderItems = [];

            foreach ($request->items as $item) {
                $variation = ProductVariation::with('product')->findOrFail($item['product_variation_id']);

                // Check stock
                if ($variation->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$variation->product->name}");
                }

                $itemSubtotal = $variation->price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $orderItems[] = [
                    'product_id' => $variation->product_id,
                    'product_variation_id' => $variation->id,
                    'product_name' => $variation->product->name,
                    'variation_name' => $variation->name,
                    'sku' => $variation->sku,
                    'quantity' => $item['quantity'],
                    'unit_price' => $variation->price,
                    'subtotal' => $itemSubtotal,
                    'total' => $itemSubtotal,
                ];

                // Reduce stock
                $variation->decrement('stock_quantity', $item['quantity']);
            }

            // Calculate delivery fee
            $deliveryFee = 0;
            if ($request->fulfillment_type === 'delivery') {
                if ($subtotal < config('services.delivery.free_delivery_threshold')) {
                    $deliveryFee = config('services.delivery.delivery_fee');
                }
            }

            $total = $subtotal + $deliveryFee;

            // Check minimum order amount
            if ($subtotal < config('services.delivery.min_order_amount')) {
                throw new \Exception('Order does not meet minimum amount requirement');
            }

            // Create order
            $order = Order::create([
                'shop_id' => \App\Services\ShopContext::getShopId(),
                'user_id' => $request->user()->id,
                'fulfillment_type' => $request->fulfillment_type,
                'delivery_slot_id' => $request->delivery_slot_id,
                'address_id' => $request->address_id,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'customer_notes' => $request->customer_notes,
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);

            // Create order items
            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            // Update delivery slot
            DeliverySlot::findOrFail($request->delivery_slot_id)->increment('current_orders');

            DB::commit();

            return response()->json([
                'message' => 'Order placed successfully',
                'order' => $order->load(['items', 'deliverySlot', 'address']),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to place order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $order = Order::with(['items.product', 'deliverySlot', 'address'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json($order);
    }

    public function cancel(Request $request, $id)
    {
        $order = Order::where('user_id', $request->user()->id)->findOrFail($id);

        if (!$order->canBeCancelled()) {
            return response()->json([
                'message' => 'Order cannot be cancelled'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Restore stock
            foreach ($order->items as $item) {
                if ($item->product_variation_id) {
                    ProductVariation::find($item->product_variation_id)
                        ->increment('stock_quantity', $item->quantity);
                }
            }

            // Update delivery slot
            if ($order->delivery_slot_id) {
                DeliverySlot::find($order->delivery_slot_id)->decrement('current_orders');
            }

            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Order cancelled successfully',
                'order' => $order,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to cancel order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
