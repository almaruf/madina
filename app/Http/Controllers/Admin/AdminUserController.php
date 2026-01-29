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
}
