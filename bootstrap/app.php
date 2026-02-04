<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'shop_admin' => \App\Http\Middleware\ShopAdminMiddleware::class,
            'customer' => \App\Http\Middleware\CustomerMiddleware::class,
            'super_admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
        ]);

        // Apply CORS middleware globally first
        $middleware->prepend(\App\Http\Middleware\Cors::class);
        
        // Apply DetectShop middleware globally to all routes
        $middleware->append(\App\Http\Middleware\DetectShop::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
