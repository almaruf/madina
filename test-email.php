#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get order 9 with all relationships
$order = \App\Models\Order::with(['user', 'shop', 'items.product', 'address', 'deliverySlot'])->find(9);

if (!$order) {
    echo "Order 9 not found\n";
    exit(1);
}

echo "Order found: {$order->order_number}\n";
echo "User email: {$order->user->email}\n";
echo "Shop: {$order->shop->name}\n";
echo "\nDispatching email job...\n";

// Dispatch the email job
\App\Jobs\SendOrderConfirmationEmail::dispatch($order);

echo "Email job dispatched to queue!\n";
echo "\nTo process the job, run: php artisan queue:work --once\n";
