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
Route::post('/admin/login', [AuthController::class, 'adminLogin']);
Route::post('/admin/verify-otp', [AuthController::class, 'adminVerifyOtp']);

// Product routes (public)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);
Route::get('/categories', [ProductController::class, 'categories']);

// Delivery slots (public)
Route::get('/delivery-slots', [DeliverySlotController::class, 'index']);

// Cart routes (public)
Route::post('/cart/validate', [\App\Http\Controllers\Api\CartController::class, 'validateCart']);

// Guest checkout order
Route::post('/orders/guest', [\App\Http\Controllers\Api\OrderController::class, 'guestStore']);

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
    Route::post('shops/{slug}/restore', [\App\Http\Controllers\Admin\ShopController::class, 'restore']);
    Route::delete('shops/{slug}/force', [\App\Http\Controllers\Admin\ShopController::class, 'forceDelete']);
    Route::get('shops/current', [\App\Http\Controllers\Admin\ShopController::class, 'current']);
    Route::patch('shops/current', [\App\Http\Controllers\Admin\ShopController::class, 'updateCurrent']);

    // Admin user management (super admin only)
    Route::apiResource('admin-users', \App\Http\Controllers\Admin\AdminUserController::class);
    
    // Regular users management
    Route::get('users', [\App\Http\Controllers\Admin\AdminUserController::class, 'allUsers']);
    Route::get('users/{id}', [\App\Http\Controllers\Admin\AdminUserController::class, 'showUser']);
    Route::patch('users/{id}', [\App\Http\Controllers\Admin\AdminUserController::class, 'updateUser']);
    Route::delete('users/{id}', [\App\Http\Controllers\Admin\AdminUserController::class, 'destroyUser']);
    Route::post('users/{id}/restore', [\App\Http\Controllers\Admin\AdminUserController::class, 'restoreUser']);
    Route::delete('users/{id}/force', [\App\Http\Controllers\Admin\AdminUserController::class, 'forceDeleteUser']);

    // Products
    Route::apiResource('products', \App\Http\Controllers\Admin\ProductController::class);
    Route::post('products/{slug}/images', [\App\Http\Controllers\Admin\ProductController::class, 'uploadImage']);
    Route::post('products/{slug}/restore', [\App\Http\Controllers\Admin\ProductController::class, 'restore']);
    Route::delete('products/{slug}/force', [\App\Http\Controllers\Admin\ProductController::class, 'forceDelete']);

    // Orders
    Route::get('orders', [\App\Http\Controllers\Admin\OrderController::class, 'index']);
    Route::get('orders/stats', [\App\Http\Controllers\Admin\OrderController::class, 'stats']);
    Route::get('orders/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'show']);
    Route::patch('orders/{id}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus']);
    Route::patch('orders/{id}/payment-status', [\App\Http\Controllers\Admin\OrderController::class, 'updatePaymentStatus']);
    Route::delete('orders/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'destroy']);
    Route::post('orders/{id}/restore', [\App\Http\Controllers\Admin\OrderController::class, 'restore']);
    Route::delete('orders/{id}/force', [\App\Http\Controllers\Admin\OrderController::class, 'forceDelete']);

    // Delivery Slots
    Route::apiResource('delivery-slots', \App\Http\Controllers\Admin\DeliverySlotController::class);
    Route::post('delivery-slots/generate', [\App\Http\Controllers\Admin\DeliverySlotController::class, 'generateSlots']);
    Route::post('delivery-slots/bulk', [\App\Http\Controllers\Admin\DeliverySlotController::class, 'bulkCreate']);

    // Categories
    Route::apiResource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    Route::post('categories/{slug}/restore', [\App\Http\Controllers\Admin\CategoryController::class, 'restore']);
    Route::delete('categories/{slug}/force', [\App\Http\Controllers\Admin\CategoryController::class, 'forceDelete']);
});
