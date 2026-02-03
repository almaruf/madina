<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ShopContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * List customers (non-PII data only)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $shopId = null;

        // Admin/super_admin can view all or selected shop
        if ($user && $user->isAdmin()) {
            $requestedShopId = $request->query('shop_id');
            if ($requestedShopId && $requestedShopId !== 'all') {
                $shopId = (int) $requestedShopId;
            }
        } else {
            $shopId = ShopContext::getShopId();
        }

        $query = User::where('role', 'customer')
            ->whereNull('deletion_requested_at')
            ->withCount(['orders' => function($q) use ($shopId) {
                if ($shopId) {
                    $q->where('shop_id', $shopId);
                }
            }]);

        // Filter by shop if specified
        if ($shopId) {
            $query->whereHas('orders', function($q) use ($shopId) {
                $q->where('shop_id', $shopId);
            });
        }

        $customers = $query->latest()->paginate(20);

        // Strip PII from response
        $customers->getCollection()->transform(function ($customer) {
            return [
                'id' => $customer->id,
                'phone' => $this->maskPhone($customer->phone),
                'email' => $customer->email ? $this->maskEmail($customer->email) : null,
                'city' => $this->getCityFromOrders($customer),
                'orders_count' => $customer->orders_count,
                'created_at' => $customer->created_at,
            ];
        });

        return response()->json($customers);
    }

    /**
     * List customers who requested deletion
     */
    public function removalRequests(Request $request)
    {
        $user = $request->user();
        $shopId = null;

        if ($user && $user->isAdmin()) {
            $requestedShopId = $request->query('shop_id');
            if ($requestedShopId && $requestedShopId !== 'all') {
                $shopId = (int) $requestedShopId;
            }
        } else {
            $shopId = ShopContext::getShopId();
        }

        $query = User::where('role', 'customer')
            ->whereNotNull('deletion_requested_at')
            ->withCount(['orders' => function($q) use ($shopId) {
                if ($shopId) {
                    $q->where('shop_id', $shopId);
                }
            }]);

        if ($shopId) {
            $query->whereHas('orders', function($q) use ($shopId) {
                $q->where('shop_id', $shopId);
            });
        }

        $customers = $query->latest('deletion_requested_at')->paginate(50);

        // Strip PII from response
        $customers->getCollection()->transform(function ($customer) {
            return [
                'id' => $customer->id,
                'phone' => $this->maskPhone($customer->phone),
                'email' => $customer->email ? $this->maskEmail($customer->email) : null,
                'city' => $this->getCityFromOrders($customer),
                'orders_count' => $customer->orders_count,
                'deletion_requested_at' => $customer->deletion_requested_at,
                'created_at' => $customer->created_at,
            ];
        });

        return response()->json($customers);
    }

    /**
     * Permanently delete customer and all PII
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $customer = User::where('role', 'customer')->findOrFail($id);

            // Delete all addresses
            $customer->addresses()->forceDelete();

            // Anonymize order data (keep orders for record but remove PII)
            $customer->orders()->update([
                'customer_notes' => null,
            ]);

            // Hard delete customer
            $customer->forceDelete();

            DB::commit();
            return response()->json(['message' => 'Customer permanently removed']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error removing customer', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Mask phone number (show last 4 digits)
     */
    private function maskPhone($phone)
    {
        if (!$phone) return null;
        $length = strlen($phone);
        if ($length <= 4) return $phone;
        return str_repeat('*', $length - 4) . substr($phone, -4);
    }

    /**
     * Mask email (show first char and domain)
     */
    private function maskEmail($email)
    {
        if (!$email) return null;
        $parts = explode('@', $email);
        if (count($parts) !== 2) return $email;
        $name = $parts[0];
        $domain = $parts[1];
        return substr($name, 0, 1) . str_repeat('*', max(0, strlen($name) - 1)) . '@' . $domain;
    }

    /**
     * Get city from most recent address in orders
     */
    private function getCityFromOrders($customer)
    {
        $order = $customer->orders()->with('address')->latest()->first();
        return $order && $order->address ? $order->address->city : null;
    }
}
