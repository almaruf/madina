<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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
