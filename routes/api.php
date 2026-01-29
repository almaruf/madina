<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\DeliverySlotController;
use App\Http\Controllers\Api\AddressController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/auth/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);

// Product routes (public)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);
Route::get('/categories', [ProductController::class, 'categories']);

// Delivery slots (public)
Route::get('/delivery-slots', [DeliverySlotController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);

    // Addresses
    Route::apiResource('addresses', AddressController::class);

    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
});

// Admin routes
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    // Shop management (super admin only)
    Route::apiResource('shops', \App\Http\Controllers\Admin\ShopController::class);
    Route::get('shops/current', [\App\Http\Controllers\Admin\ShopController::class, 'current']);
    Route::patch('shops/current', [\App\Http\Controllers\Admin\ShopController::class, 'updateCurrent']);

    // Admin user management (super admin only)
    Route::apiResource('admin-users', \App\Http\Controllers\Admin\AdminUserController::class);

    // Products
    Route::apiResource('products', \App\Http\Controllers\Admin\ProductController::class);
    Route::post('products/{id}/images', [\App\Http\Controllers\Admin\ProductController::class, 'uploadImage']);

    // Orders
    Route::get('orders', [\App\Http\Controllers\Admin\OrderController::class, 'index']);
    Route::get('orders/stats', [\App\Http\Controllers\Admin\OrderController::class, 'stats']);
    Route::get('orders/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'show']);
    Route::patch('orders/{id}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus']);
    Route::patch('orders/{id}/payment-status', [\App\Http\Controllers\Admin\OrderController::class, 'updatePaymentStatus']);

    // Delivery Slots
    Route::apiResource('delivery-slots', \App\Http\Controllers\Admin\DeliverySlotController::class);
    Route::post('delivery-slots/generate', [\App\Http\Controllers\Admin\DeliverySlotController::class, 'generateSlots']);

    // Categories
    Route::apiResource('categories', \App\Http\Controllers\Admin\CategoryController::class);
});
