<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Services\ShopContext;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Get cart items based on cart data
     */
    public function validateCart(Request $request)
    {
        $shopId = ShopContext::getShopId();

        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.variation_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $items = $request->get('items', []);
        $cartItems = [];
        $subtotal = 0;
        $totalDiscount = 0;
        $errors = [];

        foreach ($items as $item) {
            $product = Product::where('shop_id', $shopId)
                ->with(['primaryImage', 'variations'])
                ->find($item['product_id']);
            
            if (!$product) {
                $errors[] = "Product {$item['product_id']} not found";
                continue;
            }

            // Find variation
            $variation = $product->variations()->where('id', $item['variation_id'])->first();
            
            if (!$variation) {
                $errors[] = "{$product->name} - Variation not found";
                continue;
            }

            $quantity = (int) $item['quantity'];
            $unitPrice = (float) $variation->price;
            $itemTotal = $unitPrice * $quantity;

            $offer = $product->getBestOffer();
            $discountAmount = 0;
            $discountedTotal = $itemTotal;
            $discountedUnitPrice = $unitPrice;

            if ($offer) {
                $discountAmount = (float) $offer->calculateDiscount($unitPrice, $quantity);
                $discountedTotal = max(0, $itemTotal - $discountAmount);
                $discountedUnitPrice = $quantity > 0 ? ($discountedTotal / $quantity) : $unitPrice;
            }

            $subtotal += $discountedTotal;
            $totalDiscount += $discountAmount;

            $cartItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_slug' => $product->slug,
                'variation_id' => $variation->id,
                'variation_name' => $variation->name,
                'price' => $unitPrice,
                'discounted_unit_price' => $discountedUnitPrice,
                'quantity' => $quantity,
                'total' => $itemTotal,
                'discount_amount' => $discountAmount,
                'discounted_total' => $discountedTotal,
                'offer' => $offer ? [
                    'id' => $offer->id,
                    'name' => $offer->name,
                    'type' => $offer->type,
                    'discount_value' => $offer->discount_value,
                    'get_discount_percentage' => $offer->get_discount_percentage,
                    'buy_quantity' => $offer->buy_quantity,
                    'get_quantity' => $offer->get_quantity,
                    'bundle_price' => $offer->bundle_price,
                    'badge_text' => $offer->badge_text,
                    'badge_color' => $offer->badge_color,
                ] : null,
                'image_url' => $product->primaryImage?->url ?? null,
            ];
        }

        if (!empty($errors)) {
            return response()->json([
                'message' => 'Some items in your cart are unavailable',
                'errors' => $errors,
                'items' => $cartItems,
                'subtotal' => $subtotal,
                'discounts' => $totalDiscount,
            ], 422);
        }

        return response()->json([
            'items' => $cartItems,
            'subtotal' => $subtotal,
            'discounts' => $totalDiscount,
        ], 200);
    }
}
