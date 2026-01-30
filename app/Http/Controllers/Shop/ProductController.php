<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ShopContext;

class ProductController extends Controller
{
    public function index()
    {
        return view('shop.products');
    }

    public function show($slug)
    {
        $shopId = ShopContext::getShopId();

        $product = Product::where('shop_id', $shopId)
            ->where('slug', $slug)
            ->active()
            ->with(['variations', 'images', 'categories', 'primaryImage'])
            ->firstOrFail();

        // Get related products from same categories
        $relatedProducts = Product::where('shop_id', $shopId)
            ->where('id', '!=', $product->id)
            ->whereHas('categories', function ($query) use ($product) {
                $query->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->active()
            ->with(['primaryImage', 'variations'])
            ->limit(6)
            ->get();

        return view('shop.products.show', compact('product', 'relatedProducts'));
    }
}
