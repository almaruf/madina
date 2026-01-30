<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliverySlot;
use Illuminate\Http\Request;

class DeliverySlotController extends Controller
{
    public function index(Request $request)
    {
        $shopId = \App\Services\ShopContext::getShopId();
        $query = DeliverySlot::where('shop_id', $shopId)->available();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('date')) {
            $query->where('date', $request->date);
        } else {
            // Default to next 7 days
            $query->whereBetween('date', [
                now()->toDateString(),
                now()->addDays(7)->toDateString()
            ]);
        }

        $slots = $query->orderBy('date')->orderBy('start_time')->get();

        return response()->json($slots);
    }
}
