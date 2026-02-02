<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $shopId = \App\Services\ShopContext::getShopId();
        $query = Product::where('shop_id', $shopId)
            ->with(['variations', 'primaryImage', 'categories'])
            ->active();

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Featured
        if ($request->has('featured')) {
            $query->featured();
        }

        // On sale
        if ($request->has('on_sale')) {
            $query->onSale();
        }

        $products = $query->paginate($request->get('per_page', 20));

        return response()->json($products);
    }

    public function show($slug)
    {
        $shopId = \App\Services\ShopContext::getShopId();
        $product = Product::where('shop_id', $shopId)
            ->with(['variations', 'images', 'categories'])
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        return response()->json($product);
    }

    public function categories()
    {
        $shopId = \App\Services\ShopContext::getShopId();
        $categories = Category::where('shop_id', $shopId)
            ->with('children')
            ->active()
            ->root()
            ->orderBy('order')
            ->get();

        return response()->json($categories);
    }

    public function activeOffers()
    {
        $shopId = \App\Services\ShopContext::getShopId();
        $offers = \App\Models\Offer::where('shop_id', $shopId)
            ->active()
            ->valid()
            ->with(['products' => function ($query) {
                $query->active()
                    ->with(['primaryImage', 'variations'])
                    ->limit(20);
            }])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($offers);
    }
    public function showOffer($id)
    {
        $shopId = \App\Services\ShopContext::getShopId();
        $offer = \App\Models\Offer::where('shop_id', $shopId)
            ->where('id', $id)
            ->with([
                'products' => function ($query) {
                    $query->active()
                        ->with(['primaryImage', 'variations'])
                        ->orderBy('pivot_created_at', 'desc');
                },
                'buyProducts' => function ($query) {
                    $query->active()
                        ->with(['primaryImage', 'variations']);
                },
                'getProducts' => function ($query) {
                    $query->active()
                        ->with(['primaryImage', 'variations']);
                }
            ])
            ->firstOrFail();

        return response()->json($offer);
    }
}