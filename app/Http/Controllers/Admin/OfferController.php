<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Product;
use App\Services\ShopContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        $shopId = ShopContext::getShopId();
        
        $query = Offer::where('shop_id', $shopId)
            ->withCount('products')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->active()->valid();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'expired') {
                $query->where('ends_at', '<', now());
            }
        }

        $offers = $query->paginate(20);

        return response()->json($offers);
    }

    public function store(Request $request)
    {
        $shopId = ShopContext::getShopId();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage_discount,fixed_discount,bxgy_free,multibuy,bxgy_discount,flash_sale,bundle',
            'discount_value' => 'nullable|numeric|min:0',
            'buy_quantity' => 'nullable|integer|min:1',
            'get_quantity' => 'nullable|integer|min:1',
            'get_discount_percentage' => 'nullable|numeric|min:0|max:100',
            'bundle_price' => 'nullable|numeric|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'max_uses_per_customer' => 'nullable|integer|min:1',
            'total_usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'badge_text' => 'nullable|string|max:50',
            'badge_color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
            'priority' => 'nullable|integer',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['shop_id'] = $shopId;
        $data['slug'] = Str::slug($data['name']) . '-' . time();

        $productIds = $data['product_ids'] ?? [];
        unset($data['product_ids']);

        $offer = Offer::create($data);

        if (!empty($productIds)) {
            $offer->products()->attach($productIds);
        }

        return response()->json([
            'message' => 'Offer created successfully',
            'offer' => $offer->load('products')
        ], 201);
    }

    public function storeBxgy(Request $request)
    {
        $shopId = ShopContext::getShopId();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:bxgy_free,bxgy_discount',
            'buy_quantity' => 'required|integer|min:1',
            'get_quantity' => 'required|integer|min:1',
            'get_discount_percentage' => 'nullable|numeric|min:0|max:100',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'max_uses_per_customer' => 'nullable|integer|min:1',
            'total_usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'badge_text' => 'nullable|string|max:50',
            'badge_color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
            'priority' => 'nullable|integer',
            'buy_product_ids' => 'required|array|min:1',
            'buy_product_ids.*' => 'exists:products,id',
            'get_product_ids' => 'required|array|min:1',
            'get_product_ids.*' => 'exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['shop_id'] = $shopId;
        $data['slug'] = Str::slug($data['name']) . '-' . time();

        $buyProductIds = $data['buy_product_ids'];
        $getProductIds = $data['get_product_ids'];
        unset($data['buy_product_ids'], $data['get_product_ids']);

        $offer = Offer::create($data);

        // Attach buy and get products
        if (!empty($buyProductIds)) {
            $offer->buyProducts()->attach($buyProductIds);
        }
        if (!empty($getProductIds)) {
            $offer->getProducts()->attach($getProductIds);
        }

        return response()->json([
            'message' => 'BXGY offer created successfully',
            'offer' => $offer->load(['buyProducts', 'getProducts'])
        ], 201);
    }

    public function show($id)
    {
        $shopId = ShopContext::getShopId();
        
        $offer = Offer::where('shop_id', $shopId)
            ->with([
                'products' => function ($query) {
                    $query->with(['primaryImage', 'variations']);
                },
                'buyProducts' => function ($query) {
                    $query->with(['primaryImage', 'variations']);
                },
                'getProducts' => function ($query) {
                    $query->with(['primaryImage', 'variations']);
                }
            ])
            ->findOrFail($id);

        // Add buy_products and get_products to response for BXGY offers
        if (in_array($offer->type, ['bxgy_free', 'bxgy_discount'])) {
            $offer->buy_products = $offer->buyProducts;
            $offer->get_products = $offer->getProducts;
        }

        return response()->json($offer);
    }

    public function update(Request $request, $id)
    {
        $shopId = ShopContext::getShopId();
        
        $offer = Offer::where('shop_id', $shopId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'type' => 'in:percentage_discount,fixed_discount,bxgy_free,multibuy,bxgy_discount,flash_sale,bundle',
            'discount_value' => 'nullable|numeric|min:0',
            'buy_quantity' => 'nullable|integer|min:1',
            'get_quantity' => 'nullable|integer|min:1',
            'get_discount_percentage' => 'nullable|numeric|min:0|max:100',
            'bundle_price' => 'nullable|numeric|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'max_uses_per_customer' => 'nullable|integer|min:1',
            'total_usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
            'badge_text' => 'nullable|string|max:50',
            'badge_color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
            'priority' => 'nullable|integer',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'buy_product_ids' => 'nullable|array',
            'buy_product_ids.*' => 'exists:products,id',
            'get_product_ids' => 'nullable|array',
            'get_product_ids.*' => 'exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        
        if (isset($data['name']) && $data['name'] !== $offer->name) {
            $data['slug'] = Str::slug($data['name']) . '-' . time();
        }

        $productIds = $data['product_ids'] ?? null;
        $buyProductIds = $data['buy_product_ids'] ?? null;
        $getProductIds = $data['get_product_ids'] ?? null;
        unset($data['product_ids'], $data['buy_product_ids'], $data['get_product_ids']);

        $offer->update($data);

        // Handle BXGY offers
        if (in_array($offer->type, ['bxgy_free', 'bxgy_discount'])) {
            if ($buyProductIds !== null) {
                $offer->buyProducts()->sync($buyProductIds);
            }
            if ($getProductIds !== null) {
                $offer->getProducts()->sync($getProductIds);
            }
        } else {
            // Handle regular offers
            if ($productIds !== null) {
                $offer->products()->sync($productIds);
            }
        }

        return response()->json([
            'message' => 'Offer updated successfully',
            'offer' => $offer->load(['products', 'buyProducts', 'getProducts'])
        ]);
    }

    public function destroy($id)
    {
        $shopId = ShopContext::getShopId();
        
        $offer = Offer::where('shop_id', $shopId)->findOrFail($id);
        $offer->delete();

        return response()->json(['message' => 'Offer deleted successfully']);
    }

    public function toggleStatus($id)
    {
        $shopId = ShopContext::getShopId();
        
        $offer = Offer::where('shop_id', $shopId)->findOrFail($id);
        $offer->update(['is_active' => !$offer->is_active]);

        return response()->json([
            'message' => 'Offer status updated',
            'offer' => $offer
        ]);
    }

    public function products($id)
    {
        $shopId = ShopContext::getShopId();
        
        $offer = Offer::where('shop_id', $shopId)->findOrFail($id);
        $products = $offer->products()->with(['primaryImage', 'variations'])->get();

        return response()->json(['data' => $products]);
    }

    public function addProduct(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $shopId = ShopContext::getShopId();
        
        $offer = Offer::where('shop_id', $shopId)->findOrFail($id);
        
        // Verify product belongs to the same shop
        $product = \App\Models\Product::where('id', $request->product_id)
            ->where('shop_id', $shopId)
            ->firstOrFail();

        // Attach product if not already attached
        if (!$offer->products()->where('product_id', $product->id)->exists()) {
            $offer->products()->attach($product->id);
        }

        return response()->json([
            'message' => 'Product added to offer successfully',
            'product' => $product
        ]);
    }

    public function removeProduct($id, $productId)
    {
        $shopId = ShopContext::getShopId();
        
        $offer = Offer::where('shop_id', $shopId)->findOrFail($id);
        $offer->products()->detach($productId);

        return response()->json(['message' => 'Product removed from offer successfully']);
    }
}
