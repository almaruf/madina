<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
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
        $errors = [];

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            
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

            $itemTotal = $variation->price * $item['quantity'];
            $subtotal += $itemTotal;

            $cartItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_slug' => $product->slug,
                'variation_id' => $variation->id,
                'variation_name' => $variation->name,
                'price' => $variation->price,
                'quantity' => $item['quantity'],
                'total' => $itemTotal,
                'image_url' => $product->primaryImage?->url ?? null,
            ];
        }

        if (!empty($errors)) {
            return response()->json([
                'message' => 'Some items in your cart are unavailable',
                'errors' => $errors,
                'items' => $cartItems,
                'subtotal' => $subtotal,
            ], 422);
        }

        return response()->json([
            'items' => $cartItems,
            'subtotal' => $subtotal,
        ], 200);
    }
}
