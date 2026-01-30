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

    /**
     * Create a guest checkout order
     */
    public function guestStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.variation_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'delivery_slot_id' => 'required|integer|exists:delivery_slots,id',
            'payment_method' => 'required|in:cash,card,online',
            'address.first_name' => 'required|string|max:255',
            'address.last_name' => 'required|string|max:255',
            'address.phone' => 'required|string',
            'address.address_line_1' => 'required|string',
            'address.address_line_2' => 'nullable|string',
            'address.city' => 'required|string',
            'address.postcode' => 'required|string',
            'address.country' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $shopId = \App\Services\ShopContext::getShopId();
            $items = $request->get('items', []);
            $subtotal = 0;
            $deliveryFee = 5.00;
            $orderItems = [];

            // Validate stock and calculate totals
            foreach ($items as $item) {
                $variation = ProductVariation::find($item['variation_id']);

                if (!$variation || $variation->stock < $item['quantity']) {
                    return response()->json([
                        'message' => 'Insufficient stock for one or more items',
                        'errors' => ['stock' => 'Some items are out of stock']
                    ], 422);
                }

                $itemTotal = $variation->price * $item['quantity'];
                $subtotal += $itemTotal;

                $orderItems[] = [
                    'product_id' => $item['product_id'],
                    'product_variation_id' => $item['variation_id'],
                    'product_name' => $variation->product->name,
                    'variation_name' => $variation->name,
                    'price' => $variation->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemTotal,
                ];
            }

            // Create address
            $address = \App\Models\Address::create([
                'shop_id' => $shopId,
                'first_name' => $request->get('address.first_name'),
                'last_name' => $request->get('address.last_name'),
                'phone' => $request->get('address.phone'),
                'address_line_1' => $request->get('address.address_line_1'),
                'address_line_2' => $request->get('address.address_line_2'),
                'city' => $request->get('address.city'),
                'postcode' => $request->get('address.postcode'),
                'country' => $request->get('address.country'),
            ]);

            // Create order
            $order = Order::create([
                'shop_id' => $shopId,
                'user_id' => null, // Guest order
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $request->get('payment_method'),
                'fulfillment_type' => 'delivery',
                'delivery_slot_id' => $request->get('delivery_slot_id'),
                'address_id' => $address->id,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $subtotal + $deliveryFee,
                'customer_notes' => $request->get('customer_notes'),
            ]);

            // Create order items and reduce stock
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_variation_id' => $item['product_variation_id'],
                    'product_name' => $item['product_name'],
                    'variation_name' => $item['variation_name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Reduce stock
                ProductVariation::find($item['product_variation_id'])
                    ->decrement('stock', $item['quantity']);
            }

            // Increment delivery slot
            DeliverySlot::find($request->get('delivery_slot_id'))->increment('current_orders');

            DB::commit();

            return response()->json([
                'message' => 'Order created successfully',
                'id' => $order->id,
                'order_number' => $order->order_number,
                'total' => $order->total,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
