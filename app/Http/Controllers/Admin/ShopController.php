<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Services\ShopContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    public function __construct()
    {
        // Only super admins can create, update, delete shops
        $this->middleware('super_admin')->except(['current', 'updateCurrent']);
    }

    public function index(Request $request)
    {
        $query = Shop::query();

        // Handle archived filter
        if ($request->has('archived') && $request->archived == '1') {
            $query->onlyTrashed();
        }

        $shops = $query->paginate(50);
        return response()->json($shops);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:shops,slug',
            'domain' => 'nullable|string|unique:shops,domain',
            'address_line_1' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postcode' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'email' => 'required|email',
            'description' => 'nullable|string',
            'specialization' => 'nullable|string',
            'has_halal_products' => 'nullable|boolean',
            'delivery_fee' => 'nullable|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $shop = Shop::create($request->all());

        ShopContext::clearCache();

        return response()->json($shop, 201);
    }

    public function show($id)
    {
        $shop = Shop::withTrashed()->findOrFail($id);
        return response()->json($shop);
    }

    public function update(Request $request, $id)
    {
        $shop = Shop::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:shops,slug,' . $id,
            'domain' => 'nullable|string|unique:shops,domain,' . $id,
            'phone' => 'required|string|max:20',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $shop->update($request->all());

        ShopContext::clearCache();

        return response()->json($shop);
    }

    public function current()
    {
        $shop = ShopContext::getShop();

        if (!$shop) {
            return response()->json(['message' => 'No shop context'], 404);
        }

        return response()->json($shop);
    }

    public function updateCurrent(Request $request)
    {
        $shop = ShopContext::getShop();

        if (!$shop) {
            return response()->json(['message' => 'No shop context'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'phone' => 'string|max:20',
            'email' => 'email',
            'delivery_fee' => 'nullable|numeric',
            'min_order_amount' => 'nullable|numeric',
            'primary_color' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $shop->update($request->all());

        ShopContext::clearCache();

        return response()->json($shop);
    }

    public function destroy($id)
    {
        $shop = Shop::findOrFail($id);
        $shop->delete();

        ShopContext::clearCache();

        return response()->json(['message' => 'Shop archived successfully']);
    }

    public function restore($id)
    {
        $shop = Shop::onlyTrashed()->findOrFail($id);
        $shop->restore();

        ShopContext::clearCache();

        return response()->json(['message' => 'Shop restored successfully']);
    }

    public function forceDelete($id)
    {
        $shop = Shop::withTrashed()->findOrFail($id);
        $shop->forceDelete();

        ShopContext::clearCache();

        return response()->json(['message' => 'Shop permanently deleted']);
    }
}
