<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ProductController;

Route::get('/', [HomeController::class, 'index'])->name('shop.home');
Route::get('/shop/products', [ProductController::class, 'index'])->name('shop.products');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('shop.products.show');
Route::get('/cart', function () { return view('shop.cart'); })->name('shop.cart');
Route::get('/checkout', function () { return view('shop.checkout'); })->name('shop.checkout');
Route::get('/account', function () { return view('shop.account'); })->name('shop.account');
Route::get('/order-confirmation/{orderId}', function ($orderId) { 
    return view('shop.order-confirmation', ['orderId' => $orderId]); 
})->name('shop.order-confirmation');

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
    
    Route::get('/products/{slug}', function ($slug) {
        return view('admin.products.show', ['slug' => $slug]);
    })->name('admin.products.show');
    
    Route::get('/products/{slug}/edit', function ($slug) {
        return view('admin.products.edit', ['slug' => $slug]);
    })->name('admin.products.edit');
    
    Route::get('/orders', function () {
        return view('admin.orders');
    })->name('admin.orders');
    
    Route::get('/orders/{id}', function ($id) {
        return view('admin.orders.show', ['id' => $id]);
    })->name('admin.orders.show');
    
    Route::get('/categories', function () {
        return view('admin.categories');
    })->name('admin.categories');
    
    Route::get('/categories/{slug}', function ($slug) {
        return view('admin.categories.show', ['slug' => $slug]);
    })->name('admin.categories.show');
    
    Route::get('/categories/{slug}/edit', function ($slug) {
        return view('admin.categories.edit', ['slug' => $slug]);
    })->name('admin.categories.edit');
    
    Route::get('/offers', function () {
        return view('admin.offers');
    })->name('admin.offers');
    
    Route::get('/offers/create', function () {
        return view('admin.offers.create');
    })->name('admin.offers.create');
    
    Route::get('/offers/edit', function () {
        return view('admin.offers.edit');
    })->name('admin.offers.edit');
    
    Route::get('/users', function () {
        return view('admin.users');
    })->name('admin.users');
    
    Route::get('/users/{id}', function ($id) {
        return view('admin.users.show', ['id' => $id]);
    })->name('admin.users.show');
    
    Route::get('/users/{id}/edit', function ($id) {
        return view('admin.users.edit', ['id' => $id]);
    })->name('admin.users.edit');
    
    Route::get('/queue', [\App\Http\Controllers\Admin\QueueController::class, 'index'])->name('admin.queue');
    
    Route::get('/shops', function () {
        return view('admin.shops');
    })->name('admin.shops');
    
    Route::get('/shops/{slug}', function ($slug) {
        return view('admin.shops.show', ['slug' => $slug]);
    })->name('admin.shops.show');
    
    Route::get('/shops/create', function () {
        return view('admin.shops.create');
    })->name('admin.shops.create');
    
    Route::get('/shops/{slug}/edit', function ($slug) {
        return view('admin.shops.edit', ['slug' => $slug]);
    })->name('admin.shops.edit');
    
    Route::get('/delivery-slots', function () {
        return view('admin.delivery-slots');
    })->name('admin.delivery-slots');
    
    Route::get('/admin-users', function () {
        return view('admin.admin-users');
    })->name('admin.admin-users');
});
