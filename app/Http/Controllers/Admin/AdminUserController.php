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

        $query = User::where('role', 'admin');

        if ($shopId) {
            $query->where('shop_id', $shopId);
        }

        return response()->json($query->paginate(20));
    }

    /**
     * Create a new admin user for a shop
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|unique:users,phone',
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
            'shop_id' => 'required|integer|exists:shops,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $admin = User::create([
            'phone' => $request->phone,
            'email' => $request->email,
            'name' => $request->name,
            'shop_id' => $request->shop_id,
            'role' => 'admin',
            'phone_verified' => true,
            'phone_verified_at' => now(),
            'is_active' => true,
        ]);

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
     * Get all users (customers and admins) for shop admin
     */
    public function allUsers(Request $request)
    {
        $shopId = ShopContext::getShopId();
        $query = User::where('shop_id', $shopId)->withCount('orders');

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
        $shopId = ShopContext::getShopId();
        $user = User::withTrashed()->where('shop_id', $shopId)->findOrFail($id);
        
        return response()->json($user);
    }

    /**
     * Update any user (for user management page)
     */
    public function updateUser(Request $request, $id)
    {
        $shopId = ShopContext::getShopId();
        $user = User::where('shop_id', $shopId)->findOrFail($id);

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
        $user = User::where('shop_id', $shopId)->findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User archived successfully']);
    }

    /**
     * Restore an archived user
     */
    public function restoreUser($id)
    {
        $shopId = ShopContext::getShopId();
        $user = User::onlyTrashed()->where('shop_id', $shopId)->findOrFail($id);
        $user->restore();

        return response()->json(['message' => 'User restored successfully']);
    }

    /**
     * Permanently delete a user
     */
    public function forceDeleteUser($id)
    {
        $shopId = ShopContext::getShopId();
        $user = User::withTrashed()->where('shop_id', $shopId)->findOrFail($id);
        $user->forceDelete();

        return response()->json(['message' => 'User permanently deleted']);
    }
}
