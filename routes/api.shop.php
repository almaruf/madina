<?php

use App\Http\Controllers\Admin\ShopController;
use Illuminate\Support\Facades\Route;

// ... existing routes ...

// Admin shop management routes
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    // Shop management
    Route::apiResource('shops', ShopController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::get('shops/current/details', [ShopController::class, 'current']);
    Route::patch('shops/current/update', [ShopController::class, 'updateCurrent']);

    // ... rest of admin routes ...
});
