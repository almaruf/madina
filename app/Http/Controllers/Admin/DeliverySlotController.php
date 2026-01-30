<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliverySlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DeliverySlotController extends Controller
{
    public function index(Request $request)
    {
        $shopId = \App\Services\ShopContext::getShopId();
        $query = DeliverySlot::where('shop_id', $shopId);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('date')) {
            $query->where('date', $request->date);
        } else {
            $query->where('date', '>=', now()->toDateString());
        }

        $slots = $query->orderBy('date')->orderBy('start_time')->get();

        return response()->json($slots);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'type' => 'required|in:delivery,collection',
            'max_orders' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $slot = DeliverySlot::create(array_merge(
            $request->all(),
            ['shop_id' => \App\Services\ShopContext::getShopId()]
        ));

        return response()->json($slot, 201);
    }

    public function update(Request $request, $id)
    {
        $slot = DeliverySlot::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'type' => 'required|in:delivery,collection',
            'max_orders' => 'required|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $slot->update($request->all());

        return response()->json($slot);
    }

    public function destroy($id)
    {
        $slot = DeliverySlot::findOrFail($id);

        if ($slot->current_orders > 0) {
            return response()->json([
                'message' => 'Cannot delete slot with existing orders'
            ], 400);
        }

        $slot->delete();

        return response()->json(['message' => 'Delivery slot deleted successfully']);
    }

    public function generateSlots(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'type' => 'required|in:delivery,collection',
            'slots' => 'required|array',
            'slots.*.start_time' => 'required|date_format:H:i',
            'slots.*.end_time' => 'required|date_format:H:i',
            'slots.*.max_orders' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $created = [];

        while ($startDate <= $endDate) {
            foreach ($request->slots as $slot) {
                $existing = DeliverySlot::where('date', $startDate->toDateString())
                    ->where('start_time', $slot['start_time'])
                    ->where('end_time', $slot['end_time'])
                    ->where('type', $request->type)
                    ->first();

                if (!$existing) {
                    $created[] = DeliverySlot::create([
                        'shop_id' => \App\Services\ShopContext::getShopId(),
                        'date' => $startDate->toDateString(),
                        'start_time' => $slot['start_time'],
                        'end_time' => $slot['end_time'],
                        'type' => $request->type,
                        'max_orders' => $slot['max_orders'],
                    ]);
                }
            }

            $startDate->addDay();
        }

        return response()->json([
            'message' => 'Delivery slots created successfully',
            'count' => count($created)
        ], 201);
    }

    public function bulkCreate(Request $request)
    {
        // Alias for generateSlots - accepts same parameters
        return $this->generateSlots($request);
    }
}
