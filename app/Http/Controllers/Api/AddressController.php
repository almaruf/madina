<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $shopId = \App\Services\ShopContext::getShopId();
        $addresses = Address::where('shop_id', $shopId)
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json($addresses);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'nullable|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'county' => 'nullable|string|max:255',
            'postcode' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'delivery_instructions' => 'nullable|string',
            'is_default' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $existingCount = Address::where('user_id', $request->user()->id)->count();
        $shouldBeDefault = $request->is_default || $existingCount === 0;

        // If this is default, unset other defaults
        if ($shouldBeDefault) {
            Address::where('user_id', $request->user()->id)
                ->update(['is_default' => false]);
        }

        $address = Address::create(array_merge(
            $request->all(),
            [
                'shop_id' => \App\Services\ShopContext::getShopId(),
                'user_id' => $request->user()->id,
                'is_default' => $shouldBeDefault,
            ]
        ));

        return response()->json($address, 201);
    }

    public function show(Request $request, $id)
    {
        $address = Address::where('user_id', $request->user()->id)->findOrFail($id);

        return response()->json($address);
    }

    public function update(Request $request, $id)
    {
        $address = Address::where('user_id', $request->user()->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'label' => 'nullable|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'county' => 'nullable|string|max:255',
            'postcode' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'delivery_instructions' => 'nullable|string',
            'is_default' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $totalCount = Address::where('user_id', $request->user()->id)->count();
        $shouldBeDefault = $request->is_default || $totalCount === 1;

        // If this is default, unset other defaults
        if ($shouldBeDefault) {
            Address::where('user_id', $request->user()->id)
                ->where('id', '!=', $id)
                ->update(['is_default' => false]);
        }

        $address->update(array_merge(
            $request->all(),
            ['is_default' => $shouldBeDefault]
        ));

        return response()->json($address);
    }

    public function destroy(Request $request, $id)
    {
        $address = Address::where('user_id', $request->user()->id)->findOrFail($id);
        if ($address->is_default) {
            return response()->json([
                'message' => 'Default address cannot be deleted. Set another address as default first.'
            ], 422);
        }
        $address->delete();

        return response()->json(['message' => 'Address deleted successfully']);
    }
}
