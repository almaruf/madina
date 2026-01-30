<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ProductController;

Route::get('/', [HomeController::class, 'index'])->name('shop.home');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('shop.products.show');

// Admin login page
Route::get('/admin/login', function () {
    return view('admin.login');
})->name('admin.login');

// Shop login page
Route::get('/shop/login', function () {
    return view('shop.login');
})->name('shop.login');

// Admin routes
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    Route::get('/products', function () {
        return view('admin.products');
    })->name('admin.products');
    
    Route::get('/orders', function () {
        return view('admin.orders');
    })->name('admin.orders');
    
    Route::get('/delivery-slots', function () {
        return view('admin.delivery-slots');
    })->name('admin.delivery-slots');
});
