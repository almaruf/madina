<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Shop Configuration
    |--------------------------------------------------------------------------
    |
    | These values are loaded from config/shop.json file. You can customize
    | your shop by editing that JSON file instead of modifying code.
    |
    */

    'config' => function() {
        $configPath = config_path('shop.json');
        
        if (!file_exists($configPath)) {
            throw new Exception('Shop configuration file not found at: ' . $configPath);
        }
        
        $config = json_decode(file_get_contents($configPath), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON in shop configuration: ' . json_last_error_msg());
        }
        
        return $config;
    },

    // Quick access helpers (loaded from shop.json)
    'name' => function() {
        return config('shop.config')['shop_name'] ?? 'ABC Grocery Shop';
    },

    'slug' => function() {
        return config('shop.config')['shop_slug'] ?? 'abc-grocery';
    },

    'description' => function() {
        return config('shop.config')['description'] ?? 'Fresh groceries delivered to your door';
    },

    'tagline' => function() {
        return config('shop.config')['tagline'] ?? 'Quality products at affordable prices';
    },

    'phone' => env('SHOP_PHONE', function() {
        return config('shop.config')['contact']['phone'] ?? '+44 1234 567890';
    }),

    'email' => env('SHOP_EMAIL', function() {
        return config('shop.config')['contact']['email'] ?? 'info@example.com';
    }),

    'address' => function() {
        $location = config('shop.config')['location'] ?? [];
        return $location['address'] ?? 'Your Shop Address';
    },

    'full_address' => function() {
        $location = config('shop.config')['location'] ?? [];
        $parts = array_filter([
            $location['address'] ?? null,
            $location['city'] ?? null,
            $location['postcode'] ?? null,
        ]);
        return implode(', ', $parts);
    },

];
