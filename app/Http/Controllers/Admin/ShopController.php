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
        $this->middleware('super_admin')->except(['current', 'updateCurrent', 'index']);
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

    public function show($slug)
    {
        $shop = Shop::withTrashed()->where('slug', $slug)->firstOrFail();
        return response()->json($shop);
    }

    public function update(Request $request, $slug)
    {
        $shop = Shop::where('slug', $slug)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'legal_company_name' => 'nullable|string|max:255',
            'company_registration_number' => 'nullable|string|max:100',
            'slug' => 'sometimes|required|string|max:255|unique:shops,slug,' . $shop->id,
            'domain' => 'nullable|string|unique:shops,domain,' . $shop->id,
            'phone' => 'sometimes|required|string|max:20',
            'email' => 'sometimes|required|email',
            'vat_registered' => 'boolean',
            'vat_number' => 'nullable|string|max:50',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'prices_include_vat' => 'boolean',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_sort_code' => 'nullable|string|max:20',
            'bank_iban' => 'nullable|string|max:50',
            'bank_swift_code' => 'nullable|string|max:20',
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

    public function destroy($slug)
    {
        $shop = Shop::where('slug', $slug)->firstOrFail();
        $shop->delete();

        ShopContext::clearCache();

        return response()->json(['message' => 'Shop archived successfully']);
    }

    public function restore($slug)
    {
        $shop = Shop::onlyTrashed()->where('slug', $slug)->firstOrFail();
        $shop->restore();

        ShopContext::clearCache();

        return response()->json(['message' => 'Shop restored successfully']);
    }

    public function forceDelete($slug)
    {
        $shop = Shop::withTrashed()->where('slug', $slug)->firstOrFail();
        $shop->forceDelete();

        ShopContext::clearCache();

        return response()->json(['message' => 'Shop permanently deleted']);
    }
}
