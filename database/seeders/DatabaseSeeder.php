<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Shop;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductImage;
use App\Models\ShopBanner;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\DeliverySlot;
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
            'phone' => '+4407849261469',
            'email' => 'maruf.sylhet@gmail.com',
            'name' => 'Al Maruf',
            'role' => 'super_admin',
            'phone_verified' => true,
            'phone_verified_at' => now(),
            'is_active' => true,
            'shop_id' => null, // Super admin is not tied to a specific shop
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
            'is_featured' => true,
            'image' => 'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?w=800',
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
            'is_featured' => true,
            'image' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=800',
        ]);

        $vegetables = Category::create([
            'shop_id' => $shop->id,
            'name' => 'Fresh Vegetables',
            'slug' => 'vegetables',
            'description' => 'Fresh vegetables daily',
            'is_active' => true,
            'is_featured' => true,
            'image' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=800',
        ]);

        // Create banners
        ShopBanner::create([
            'shop_id' => $shop->id,
            'title' => 'Welcome to ABC Grocery Shop',
            'description' => 'Fresh products delivered to your door. Order now!',
            'image' => 'https://images.unsplash.com/photo-1534723452862-4c874018d66d?w=1200',
            'order' => 1,
            'is_active' => true,
        ]);

        ShopBanner::create([
            'shop_id' => $shop->id,
            'title' => 'Fresh Meat Daily',
            'description' => 'Premium quality halal meat available',
            'image' => 'https://images.unsplash.com/photo-1603048588665-791ca8aea617?w=1200',
            'order' => 2,
            'is_active' => true,
        ]);

        ShopBanner::create([
            'shop_id' => $shop->id,
            'title' => 'Groceries at Best Prices',
            'description' => 'Save on your weekly shopping',
            'image' => 'https://images.unsplash.com/photo-1601599561213-832382fd07ba?w=1200',
            'order' => 3,
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
            'is_featured' => true,
            'times_purchased' => 45,
        ]);

        $chickenBreast->categories()->attach([$meat->id, $chicken->id]);

        ProductImage::create([
            'product_id' => $chickenBreast->id,
            'url' => 'https://images.unsplash.com/photo-1587593810167-a84920ea0781?w=600',
            'path' => 'products/chicken-breast.jpg',
            'is_primary' => true,
            'order' => 1,
        ]);

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

        // Add more featured products
        $products = [
            [
                'name' => 'Premium Beef Steak',
                'slug' => 'beef-steak',
                'description' => 'Premium quality beef steak',
                'type' => 'meat',
                'meat_type' => 'beef',
                'is_halal' => true,
                'is_featured' => true,
                'times_purchased' => 38,
                'image' => 'https://images.unsplash.com/photo-1588168333986-5078d3ae3976?w=600',
                'categories' => [$meat->id, $beef->id],
                'variations' => [['name' => '500g', 'price' => 12.99, 'size' => 500, 'size_unit' => 'g']],
            ],
            [
                'name' => 'Organic Tomatoes',
                'slug' => 'organic-tomatoes',
                'description' => 'Fresh organic tomatoes',
                'type' => 'fresh',
                'is_featured' => true,
                'times_purchased' => 52,
                'image' => 'https://images.unsplash.com/photo-1546470427-72-50a?w=600',
                'categories' => [$vegetables->id],
                'variations' => [['name' => '1kg', 'price' => 2.99, 'size' => 1, 'size_unit' => 'kg']],
            ],
            [
                'name' => 'Fresh Milk',
                'slug' => 'fresh-milk',
                'description' => 'Full cream fresh milk',
                'type' => 'dairy',
                'is_featured' => true,
                'times_purchased' => 67,
                'image' => 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=600',
                'categories' => [$groceries->id],
                'variations' => [['name' => '2L', 'price' => 1.99, 'size' => 2, 'size_unit' => 'l']],
            ],
            [
                'name' => 'Brown Bread',
                'slug' => 'brown-bread',
                'description' => 'Freshly baked brown bread',
                'type' => 'bakery',
                'is_featured' => true,
                'times_purchased' => 89,
                'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=600',
                'categories' => [$groceries->id],
                'variations' => [['name' => '800g', 'price' => 1.49, 'size' => 800, 'size_unit' => 'g']],
            ],
            [
                'name' => 'Free Range Eggs',
                'slug' => 'free-range-eggs',
                'description' => 'Fresh free range eggs',
                'type' => 'standard',
                'is_featured' => true,
                'times_purchased' => 72,
                'image' => 'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f?w=600',
                'categories' => [$groceries->id],
                'variations' => [['name' => '12 pack', 'price' => 3.49, 'size' => 12, 'size_unit' => 'pack']],
            ],
            [
                'name' => 'Bananas',
                'slug' => 'bananas',
                'description' => 'Fresh bananas',
                'type' => 'fresh',
                'times_purchased' => 95,
                'image' => 'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?w=600',
                'categories' => [$vegetables->id],
                'variations' => [['name' => '1kg', 'price' => 1.29, 'size' => 1, 'size_unit' => 'kg']],
            ],
            [
                'name' => 'Olive Oil',
                'slug' => 'olive-oil',
                'description' => 'Extra virgin olive oil',
                'type' => 'standard',
                'times_purchased' => 41,
                'image' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=600',
                'categories' => [$groceries->id],
                'variations' => [['name' => '500ml', 'price' => 6.99, 'size' => 500, 'size_unit' => 'ml']],
            ],
            [
                'name' => 'Whole Chicken',
                'slug' => 'whole-chicken',
                'description' => 'Fresh whole chicken',
                'type' => 'meat',
                'meat_type' => 'chicken',
                'is_halal' => true,
                'times_purchased' => 33,
                'image' => 'https://images.unsplash.com/photo-1604503468506-a8da13d82791?w=600',
                'categories' => [$meat->id, $chicken->id],
                'variations' => [['name' => '1.5kg', 'price' => 7.99, 'size' => 1.5, 'size_unit' => 'kg']],
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create([
                'shop_id' => $shop->id,
                'name' => $productData['name'],
                'slug' => $productData['slug'],
                'description' => $productData['description'],
                'type' => $productData['type'],
                'is_halal' => $productData['is_halal'] ?? false,
                'meat_type' => $productData['meat_type'] ?? null,
                'is_active' => true,
                'is_featured' => $productData['is_featured'] ?? false,
                'times_purchased' => $productData['times_purchased'] ?? 0,
            ]);

            $product->categories()->attach($productData['categories']);

            ProductImage::create([
                'product_id' => $product->id,
                'url' => $productData['image'],
                'path' => 'products/' . $productData['slug'] . '.jpg',
                'is_primary' => true,
                'order' => 1,
            ]);

            foreach ($productData['variations'] as $variation) {
                ProductVariation::create([
                    'product_id' => $product->id,
                    'name' => $variation['name'],
                    'size' => $variation['size'],
                    'size_unit' => $variation['size_unit'],
                    'price' => $variation['price'],
                    'stock_quantity' => 50,
                    'is_default' => true,
                ]);
            }
        }

        // Create delivery slots for next 7 days
        $startDate = now();
        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            
            DeliverySlot::create([
                'shop_id' => $shop->id,
                'date' => $date->toDateString(),
                'start_time' => '09:00',
                'end_time' => '12:00',
                'type' => 'delivery',
                'max_orders' => 10,
                'current_orders' => 0,
                'is_active' => true,
            ]);

            DeliverySlot::create([
                'shop_id' => $shop->id,
                'date' => $date->toDateString(),
                'start_time' => '14:00',
                'end_time' => '18:00',
                'type' => 'delivery',
                'max_orders' => 15,
                'current_orders' => 0,
                'is_active' => true,
            ]);
        }

        // Get customer for orders
        $customer = User::where('role', 'customer')->where('shop_id', $shop->id)->first();

        // Create customer address
        $address = Address::create([
            'shop_id' => $shop->id,
            'user_id' => $customer->id,
            'address_line_1' => '123 High Street',
            'address_line_2' => 'Flat 4B',
            'city' => 'London',
            'postcode' => 'SW1A 1AA',
            'is_default' => true,
        ]);

        // Get products and variations for orders
        $allProducts = Product::where('shop_id', $shop->id)->with('variations')->get();
        $deliverySlot = DeliverySlot::where('shop_id', $shop->id)->first();

        // Create sample orders
        $orderStatuses = [
            ['status' => 'pending', 'payment_status' => 'pending'],
            ['status' => 'confirmed', 'payment_status' => 'paid'],
            ['status' => 'processing', 'payment_status' => 'paid'],
            ['status' => 'ready', 'payment_status' => 'paid'],
            ['status' => 'out_for_delivery', 'payment_status' => 'paid'],
            ['status' => 'delivered', 'payment_status' => 'paid'],
            ['status' => 'completed', 'payment_status' => 'paid'],
        ];

        foreach ($orderStatuses as $index => $orderStatus) {
            // Select 2-4 random products for each order
            $orderProducts = $allProducts->random(rand(2, 4));
            
            $subtotal = 0;
            $items = [];
            
            foreach ($orderProducts as $product) {
                $variation = $product->variations->first();
                if (!$variation) continue;
                
                $quantity = rand(1, 3);
                $price = $variation->price;
                $itemTotal = $price * $quantity;
                $subtotal += $itemTotal;
                
                $items[] = [
                    'product' => $product,
                    'variation' => $variation,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $itemTotal,
                ];
            }
            
            $deliveryFee = 3.99;
            $total = $subtotal + $deliveryFee;
            
            // Create order
            $order = Order::create([
                'shop_id' => $shop->id,
                'user_id' => $customer->id,
                'order_number' => 'ORD-' . str_pad($index + 1, 6, '0', STR_PAD_LEFT),
                'status' => $orderStatus['status'],
                'payment_status' => $orderStatus['payment_status'],
                'payment_method' => $orderStatus['payment_status'] === 'paid' ? 'card' : 'cash',
                'fulfillment_type' => 'delivery',
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'address_id' => $address->id,
                'delivery_slot_id' => $deliverySlot->id,
                'created_at' => now()->subDays(7 - $index),
                'updated_at' => now()->subDays(7 - $index),
            ]);
            
            // Create order items
            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'product_variation_id' => $item['variation']->id,
                    'product_name' => $item['product']->name,
                    'variation_name' => $item['variation']->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['total'],
                    'total' => $item['total'],
                ]);
            }
        }
    }
}
