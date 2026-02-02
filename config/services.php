<?php

return [

    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_TOKEN'),
        'from' => env('TWILIO_FROM'),
    ],

    'shop' => [
        'phone' => env('SHOP_PHONE', '+44 1234 567890'),
        'email' => env('SHOP_EMAIL', 'info@example.com'),
        'address' => env('SHOP_ADDRESS', 'Your Shop Address'),
    ],

    'delivery' => [
        'radius_km' => env('DELIVERY_RADIUS_KM', 10),
        'min_order_amount' => env('MIN_ORDER_AMOUNT', 20.00),
        'delivery_fee' => env('DELIVERY_FEE', 3.99),
        'free_delivery_threshold' => env('FREE_DELIVERY_THRESHOLD', 50.00),
    ],

    'otp' => [
        'expiry_minutes' => 10,
        'max_attempts' => 3,
    ],

    'vat' => [
        'default_rate' => env('VAT_RATE', 20.00),
    ],

];
