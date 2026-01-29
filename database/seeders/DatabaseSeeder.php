<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Shop;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create super admin user (can create and manage shops)
        $superAdmin = User::create([
            'phone' => '4407849261469',
            'email' => 'maruf.sylhet@gmail.com',
            'name' => 'Al Maruf',
            'role' => 'super_admin',
            'phone_verified' => true,
            'phone_verified_at' => now(),
            'is_active' => true,
        ]);

        // Create first shop
        $shop = Shop::create([
            'name' => 'ABC Grocery Shop',
            'slug' => 'abc-grocery',
            'domain' => 'localhost',
            'description' => 'Fresh groceries delivered to your door',
            'tagline' => 'Quality products at affordable prices',
            'address_line_1' => 'Your Shop Address',
            'city' => 'Your City',
            'postcode' => 'YOUR POSTCODE',
            'country' => 'United Kingdom',
            'phone' => '+44 1234 567890',
            'email' => 'info@example.com',
            'currency' => 'GBP',
            'currency_symbol' => '£',
            'is_active' => true,
        ]);

        // Create admin user for the shop (can manage products, orders, etc for this shop)
        User::create([
            'shop_id' => $shop->id,
            'phone' => '+441234567890',
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'phone_verified' => true,
            'phone_verified_at' => now(),
            'is_active' => true,
        ]);

        // Create test customer
        User::create([
            'shop_id' => $shop->id,
            'phone' => '+441234567891',
            'name' => 'Test Customer',
            'email' => 'customer@test.com',
            'role' => 'customer',
            'phone_verified' => true,
            'phone_verified_at' => now(),
            'is_active' => true,
        ]);

        // Create categories
        $meat = Category::create([
            'shop_id' => $shop->id,
            'name' => 'Meat',
            'slug' => 'meat',
            'description' => 'Fresh meat products',
            'is_active' => true,
        ]);

        $chicken = Category::create([
            'shop_id' => $shop->id,
            'name' => 'Chicken',
            'slug' => 'chicken',
            'parent_id' => $meat->id,
            'is_active' => true,
        ]);

        $beef = Category::create([
            'shop_id' => $shop->id,
            'name' => 'Beef',
            'slug' => 'beef',
            'parent_id' => $meat->id,
            'is_active' => true,
        ]);

        $groceries = Category::create([
            'shop_id' => $shop->id,
            'name' => 'Groceries',
            'slug' => 'groceries',
            'description' => 'Essential grocery items',
            'is_active' => true,
        ]);

        // Create sample products
        $chickenBreast = Product::create([
            'shop_id' => $shop->id,
            'name' => 'Chicken Breast',
            'slug' => 'chicken-breast',
            'description' => 'Fresh chicken breast',
            'type' => 'meat',
            'is_halal' => false,
            'meat_type' => 'chicken',
            'is_active' => true,
        ]);

        $chickenBreast->categories()->attach([$meat->id, $chicken->id]);

        ProductVariation::create([
            'product_id' => $chickenBreast->id,
            'name' => '500g',
            'size' => 500,
            'size_unit' => 'g',
            'price' => 4.99,
            'stock_quantity' => 50,
            'is_default' => true,
        ]);

        ProductVariation::create([
            'product_id' => $chickenBreast->id,
            'name' => '1kg',
            'size' => 1,
            'size_unit' => 'kg',
            'price' => 8.99,
            'stock_quantity' => 30,
        ]);

        // Add more sample products as needed
    }
}
