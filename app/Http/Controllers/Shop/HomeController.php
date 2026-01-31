<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ShopBanner;
use App\Services\ShopContext;

class HomeController extends Controller
{
    public function index()
    {
        $shopId = ShopContext::getShopId();

        $banners = ShopBanner::where('shop_id', $shopId)
            ->active()
            ->get();


        $featuredCategories = Category::where('shop_id', $shopId)
            ->featured()
            ->active()
            ->root()
            ->with('children')
            ->limit(3)
            ->get();

        $otherCategories = Category::where('shop_id', $shopId)
            ->active()
            ->root()
            ->whereNotIn('id', $featuredCategories->pluck('id')->all())
            ->orderBy('order')->get();

        $featuredProducts = Product::where('shop_id', $shopId)
            ->featured()
            ->active()
            ->with(['primaryImage', 'variations', 'categories'])
            ->get();

        $popularProducts = Product::where('shop_id', $shopId)
            ->popular(15)
            ->active()
            ->with(['primaryImage', 'variations', 'categories'])
            ->get();

        return view('shop.index', compact(
            'banners',
            'featuredCategories',
            'otherCategories',
            'featuredProducts',
            'popularProducts'
        ));
    }
}
