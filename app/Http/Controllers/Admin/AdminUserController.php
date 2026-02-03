<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ShopContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminUserController extends Controller
{
    public function __construct()
    {
        // Super admin can manage all admin users
        $this->middleware('super_admin');
    }

    /**
     * List admin users for a shop
     */
    public function index(Request $request)
    {
        $shopId = $request->query('shop_id');

        if (!$shopId && !auth()->user()->isSuperAdmin()) {
            $shopId = ShopContext::getShopId();
        }

        // Include all admin roles: super_admin, shop_admin, shop_manager, admin
        $query = User::whereIn('role', ['super_admin', 'shop_admin', 'shop_manager', 'admin'])
                     ->with('shop');

        if ($shopId) {
            $query->where(function($q) use ($shopId) {
                $q->where('shop_id', $shopId)
                  ->orWhere('role', 'super_admin'); // Always include super_admin
            });
        }

        return response()->json($query->paginate(20));
    }

    /**
     * Create a new admin user for a shop
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => [
                'required',
                'string',
                'unique:users,phone',
                'regex:/^\+44[0-9]{10}$/'
            ],
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
            'shop_id' => 'required|integer|exists:shops,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Determine if this is owner/staff (needs shop_id) or admin (no shop_id)
        $role = $request->input('role', 'admin');
        $userData = [
            'phone' => $request->phone,
            'email' => $request->email,
            'name' => $request->name,
            'role' => $role,
            'phone_verified' => true,
            'phone_verified_at' => now(),
            'is_active' => true,
        ];
        
        // Only owner and staff are tied to specific shops
        if (in_array($role, ['owner', 'staff'])) {
            $userData['shop_id'] = $request->shop_id;
        }
        
        $admin = User::create($userData);

        return response()->json([
            'message' => 'Admin user created successfully',
            'user' => $admin
        ], 201);
    }

    /**
     * Get admin user details
     */
    public function show($id)
    {
        $user = User::where('role', 'admin')->findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update admin user
     */
    public function update(Request $request, $id)
    {
        $user = User::where('role', 'admin')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'email' => 'email|unique:users,email,' . $id,
            'name' => 'string|max:255',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update($request->only(['email', 'name', 'is_active']));

        return response()->json([
            'message' => 'Admin user updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Delete admin user
     */
    public function destroy($id)
    {
        $user = User::where('role', 'admin')->findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Admin user deleted successfully']);
    }

    /**
     * Get all users (owner/staff only) for shop admin
     */
    public function allUsers(Request $request)
    {
        $shopId = ShopContext::getShopId();
        
        // Get users associated with this shop:
        // - Owner/Staff with this shop_id only
        // - Customers are managed separately in CustomerController
        $query = User::whereIn('role', ['owner', 'staff'])
            ->where('shop_id', $shopId)
            ->withCount(['orders' => function($orderQuery) use ($shopId) {
                $orderQuery->where('shop_id', $shopId);
            }]);

        // Handle archived filter
        if ($request->has('archived') && $request->archived == '1') {
            $query->onlyTrashed();
        }

        // Search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        return response()->json($query->latest()->paginate(50));
    }

    /**
     * Get a single user (for detail view)
     */
    public function showUser($id)
    {
        $authUser = auth()->user();
        
        // Super admin can view any user
        if ($authUser->role === 'super_admin') {
            return response()->json(User::withTrashed()->with('shop')->findOrFail($id));
        }
        
        // Regular admins can only view users in their shop, customers who ordered, or other admins
        $shopId = ShopContext::getShopId();
        $user = User::withTrashed()->with('shop')
            ->where(function($q) use ($shopId) {
                $q->where('shop_id', $shopId)
                  ->orWhere('role', 'admin')
                  ->orWhereHas('orders', function($orderQuery) use ($shopId) {
                      $orderQuery->where('shop_id', $shopId);
                  });
            })
            ->findOrFail($id);
        
        return response()->json($user);
    }

    /**
     * Update any user (for user management page)
     */
    public function updateUser(Request $request, $id)
    {
        $authUser = auth()->user();
        
        // Find the user to update
        if ($authUser->role === 'super_admin') {
            $user = User::findOrFail($id);
        } else {
            $shopId = ShopContext::getShopId();
            $user = User::where(function($q) use ($shopId) {
                $q->where('shop_id', $shopId)
                  ->orWhere('role', 'admin')
                  ->orWhereHas('orders', function($orderQuery) use ($shopId) {
                      $orderQuery->where('shop_id', $shopId);
                  });
            })->findOrFail($id);
        }
        
        // Super admin can only be edited by themselves
        if ($user->role === 'super_admin' && $authUser->id !== $user->id) {
            return response()->json([
                'message' => 'Super admin users can only be edited by themselves.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'role' => 'sometimes|in:customer,admin,shop_manager,shop_admin',
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update($request->only(['role', 'name', 'email', 'is_active']));

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Archive (soft delete) a user
     */
    public function destroyUser($id)
    {
        $shopId = ShopContext::getShopId();
        $user = User::where(function($q) use ($shopId) {
            $q->where('shop_id', $shopId)
              ->orWhere('role', 'admin')
              ->orWhereHas('orders', function($orderQuery) use ($shopId) {
                  $orderQuery->where('shop_id', $shopId);
              });
        })->findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User archived successfully']);
    }

    /**
     * Restore an archived user
     */
    public function restoreUser($id)
    {
        $shopId = ShopContext::getShopId();
        $user = User::onlyTrashed()->where(function($q) use ($shopId) {
            $q->where('shop_id', $shopId)
              ->orWhere('role', 'admin')
              ->orWhereHas('orders', function($orderQuery) use ($shopId) {
                  $orderQuery->where('shop_id', $shopId);
              });
        })->findOrFail($id);
        $user->restore();

        return response()->json(['message' => 'User restored successfully']);
    }

    /**
     * Permanently delete a user
     */
    public function forceDeleteUser($id)
    {
        $shopId = ShopContext::getShopId();
        $user = User::withTrashed()->where(function($q) use ($shopId) {
            $q->where('shop_id', $shopId)
              ->orWhere('role', 'admin')
              ->orWhereHas('orders', function($orderQuery) use ($shopId) {
                  $orderQuery->where('shop_id', $shopId);
              });
        })->findOrFail($id);
        $user->forceDelete();

        return response()->json(['message' => 'User permanently deleted']);
    }

    /**
     * Get all addresses for a specific user (admin only)
     */
    public function getUserAddresses($userId)
    {
        $authUser = auth()->user();
        
        // Super admin can view any user's addresses
        if ($authUser->role === 'super_admin') {
            $addresses = \App\Models\Address::where('user_id', $userId)->get();
        } else {
            // Regular admins can only view addresses for users in their shop or customers who ordered from their shop
            $shopId = ShopContext::getShopId();
            $user = User::where(function($q) use ($shopId) {
                $q->where('shop_id', $shopId)
                  ->orWhere('role', 'admin')
                  ->orWhereHas('orders', function($orderQuery) use ($shopId) {
                      $orderQuery->where('shop_id', $shopId);
                  });
            })->findOrFail($userId);
            $addresses = \App\Models\Address::where('user_id', $userId)->get();
        }
        
        return response()->json($addresses);
    }

    /**
     * Create an address for a specific user (admin only)
     */
    public function createUserAddress(Request $request, $userId)
    {
        $authUser = auth()->user();
        
        // Verify admin has permission to manage this user
        if ($authUser->role === 'super_admin') {
            $user = User::findOrFail($userId);
        } else {
            $shopId = ShopContext::getShopId();
            $user = User::where(function($q) use ($shopId) {
                $q->where('shop_id', $shopId)
                  ->orWhere('role', 'admin')
                  ->orWhereHas('orders', function($orderQuery) use ($shopId) {
                      $orderQuery->where('shop_id', $shopId);
                  });
            })->findOrFail($userId);
        }

        $validator = Validator::make($request->all(), [
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'postcode' => 'required|string|max:20',
            'country' => 'nullable|string|max:255',
            'is_default' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // If this is default, unset other defaults for this user
        if ($request->is_default) {
            \App\Models\Address::where('user_id', $userId)
                ->update(['is_default' => false]);
        }

        $shopId = ShopContext::getShopId();
        $address = \App\Models\Address::create(array_merge(
            $request->all(),
            [
                'shop_id' => $shopId,
                'user_id' => $userId
            ]
        ));

        return response()->json($address, 201);
    }

    /**
     * Update a user's address (admin only)
     */
    public function updateUserAddress(Request $request, $userId, $addressId)
    {
        $authUser = auth()->user();
        
        // Verify admin has permission to manage this user
        if ($authUser->role === 'super_admin') {
            $user = User::findOrFail($userId);
        } else {
            $shopId = ShopContext::getShopId();
            $user = User::where(function($q) use ($shopId) {
                $q->where('shop_id', $shopId)
                  ->orWhere('role', 'admin')
                  ->orWhereHas('orders', function($orderQuery) use ($shopId) {
                      $orderQuery->where('shop_id', $shopId);
                  });
            })->findOrFail($userId);
        }

        $address = \App\Models\Address::where('user_id', $userId)->findOrFail($addressId);

        $validator = Validator::make($request->all(), [
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'postcode' => 'required|string|max:20',
            'country' => 'nullable|string|max:255',
            'is_default' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // If this is default, unset other defaults for this user
        if ($request->is_default) {
            \App\Models\Address::where('user_id', $userId)
                ->where('id', '!=', $addressId)
                ->update(['is_default' => false]);
        }

        $address->update($request->all());

        return response()->json($address);
    }

    /**
     * Delete a user's address (admin only)
     */
    public function deleteUserAddress($userId, $addressId)
    {
        $authUser = auth()->user();
        
        // Verify admin has permission to manage this user
        if ($authUser->role === 'super_admin') {
            $user = User::findOrFail($userId);
        } else {
            $shopId = ShopContext::getShopId();
            $user = User::where(function($q) use ($shopId) {
                $q->where('shop_id', $shopId)
                  ->orWhere('role', 'admin')
                  ->orWhereHas('orders', function($orderQuery) use ($shopId) {
                      $orderQuery->where('shop_id', $shopId);
                  });
            })->findOrFail($userId);
        }

        $address = \App\Models\Address::where('user_id', $userId)->findOrFail($addressId);
        $address->delete();

        return response()->json(['message' => 'Address deleted successfully']);
    }
}
