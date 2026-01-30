<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ProductController;

Route::get('/', [HomeController::class, 'index'])->name('shop.home');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('shop.products.show');
Route::get('/cart', function () { return view('shop.cart'); })->name('shop.cart');
Route::get('/checkout', function () { return view('shop.checkout'); })->name('shop.checkout');

// Generic login redirect (for Sanctum)
Route::get('/login', function () {
    return redirect()->route('shop.login');
})->name('login');

// Admin login page
Route::get('/admin/login', function () {
    return view('admin.login');
})->name('admin.login');

// Shop login page
Route::get('/shop/login', function () {
    return view('shop.login');
})->name('shop.login');

// Admin routes (authentication checked client-side via token in localStorage)
Route::prefix('admin')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    Route::get('/products', function () {
        return view('admin.products');
    })->name('admin.products');
    
    Route::get('/orders', function () {
        return view('admin.orders');
    })->name('admin.orders');
    
    Route::get('/categories', function () {
        return view('admin.categories');
    })->name('admin.categories');
    
    Route::get('/users', function () {
        return view('admin.users');
    })->name('admin.users');
    
    Route::get('/shops', function () {
        return view('admin.shops');
    })->name('admin.shops');
    
    Route::get('/shops/create', function () {
        return view('admin.shops.create');
    })->name('admin.shops.create');
    
    Route::get('/shops/{id}/edit', function ($id) {
        return view('admin.shops.edit', ['shopId' => $id]);
    })->name('admin.shops.edit');
    
    Route::get('/delivery-slots', function () {
        return view('admin.delivery-slots');
    })->name('admin.delivery-slots');
});
