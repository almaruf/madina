<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $this->otpService->sendOtp($request->phone);

            return response()->json([
                'message' => 'OTP sent successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send OTP',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!$this->otpService->verifyOtp($request->phone, $request->otp)) {
            return response()->json([
                'message' => 'Invalid or expired OTP'
            ], 401);
        }

        // Prevent customer registration from public API
        // Only super_admin can create admin users via separate endpoint
        if ($request->has('role') && in_array($request->role, ['admin', 'super_admin'])) {
            return response()->json([
                'message' => 'Invalid role. Contact super admin to create admin accounts.'
            ], 403);
        }

        // Find or create user
        $user = User::where('shop_id', app(\App\Services\ShopContext::class)->getShopId())
            ->where('phone', $request->phone)
            ->first();

        if (!$user) {
            $user = User::create([
                'shop_id' => app(\App\Services\ShopContext::class)->getShopId(),
                'phone' => $request->phone,
                'phone_verified' => true,
                'phone_verified_at' => now(),
                'role' => 'customer',
            ]);
        } else if (!$user->phone_verified) {
            $user->update([
                'phone_verified' => true,
                'phone_verified_at' => now(),
            ]);
        }

        // Create token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
