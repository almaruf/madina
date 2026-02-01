<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Models\Offer;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OffersSeeder extends Seeder
{
    public function run(): void
    {
        $shop = Shop::first();
        
        if (!$shop) {
            $this->command->error('No shop found. Please run DatabaseSeeder first.');
            return;
        }

        $products = Product::where('shop_id', $shop->id)->get();
        
        if ($products->count() < 10) {
            $this->command->error('Not enough products found. Please run ProductsSeeder first.');
            return;
        }

        // 1. Percentage Discount - 20% off fruits
        $fruitOffer = Offer::create([
            'shop_id' => $shop->id,
            'name' => 'Fresh Fruits Sale',
            'slug' => 'fresh-fruits-sale',
            'description' => '20% off all fresh fruits this week',
            'type' => 'percentage_discount',
            'discount_value' => 20,
            'starts_at' => Carbon::now(),
            'ends_at' => Carbon::now()->addDays(7),
            'badge_text' => '20% OFF',
            'badge_color' => '#DC2626',
            'is_active' => true,
            'priority' => 10,
        ]);
        
        // Attach fruit products
        $fruits = $products->filter(function($p) {
            return str_contains(strtolower($p->name), 'banana') || 
                   str_contains(strtolower($p->name), 'apple') || 
                   str_contains(strtolower($p->name), 'orange') || 
                   str_contains(strtolower($p->name), 'strawberr');
        });
        $fruitOffer->products()->attach($fruits->pluck('id'));

        // 2. Buy 2 Get 1 Free - Dairy products
        $bogoOffer = Offer::create([
            'shop_id' => $shop->id,
            'name' => 'Dairy BOGO',
            'slug' => 'dairy-bogo',
            'description' => 'Buy 2 get 1 free on selected dairy items',
            'type' => 'bxgy_free',
            'buy_quantity' => 2,
            'get_quantity' => 1,
            'starts_at' => Carbon::now(),
            'ends_at' => Carbon::now()->addDays(14),
            'badge_text' => 'B2G1',
            'badge_color' => '#059669',
            'is_active' => true,
            'priority' => 9,
        ]);
        
        $dairy = $products->filter(function($p) {
            return str_contains(strtolower($p->name), 'milk') || 
                   str_contains(strtolower($p->name), 'cheese') || 
                   str_contains(strtolower($p->name), 'yogurt') || 
                   str_contains(strtolower($p->name), 'butter');
        });
        $bogoOffer->products()->attach($dairy->pluck('id'));

        // 3. Fixed Discount - £2 off meat
        $meatOffer = Offer::create([
            'shop_id' => $shop->id,
            'name' => 'Meat Savings',
            'slug' => 'meat-savings',
            'description' => '£2 off premium meat products',
            'type' => 'fixed_discount',
            'discount_value' => 2.00,
            'starts_at' => Carbon::now(),
            'ends_at' => Carbon::now()->addDays(5),
            'badge_text' => '£2 OFF',
            'badge_color' => '#7C3AED',
            'min_purchase_amount' => 5.00,
            'is_active' => true,
            'priority' => 8,
        ]);
        
        $meat = $products->filter(function($p) {
            return str_contains(strtolower($p->name), 'chicken') || 
                   str_contains(strtolower($p->name), 'beef') || 
                   str_contains(strtolower($p->name), 'lamb');
        });
        $meatOffer->products()->attach($meat->pluck('id'));

        // 4. Multi-buy Deal - 3 for £5 snacks
        $multibuyOffer = Offer::create([
            'shop_id' => $shop->id,
            'name' => 'Snack Attack',
            'slug' => 'snack-attack',
            'description' => 'Any 3 snacks for just £5',
            'type' => 'multibuy',
            'buy_quantity' => 3,
            'bundle_price' => 5.00,
            'starts_at' => Carbon::now(),
            'ends_at' => Carbon::now()->addDays(30),
            'badge_text' => '3 FOR £5',
            'badge_color' => '#F59E0B',
            'is_active' => true,
            'priority' => 7,
        ]);
        
        $snacks = $products->filter(function($p) {
            return str_contains(strtolower($p->name), 'chips') || 
                   str_contains(strtolower($p->name), 'chocolate') || 
                   str_contains(strtolower($p->name), 'nuts');
        });
        $multibuyOffer->products()->attach($snacks->pluck('id'));

        // 5. Flash Sale - 30% off frozen items
        $flashOffer = Offer::create([
            'shop_id' => $shop->id,
            'name' => 'Frozen Flash Sale',
            'slug' => 'frozen-flash-sale',
            'description' => '30% off frozen foods - today only!',
            'type' => 'flash_sale',
            'discount_value' => 30,
            'starts_at' => Carbon::now(),
            'ends_at' => Carbon::now()->addHours(24),
            'badge_text' => 'FLASH 30%',
            'badge_color' => '#EF4444',
            'total_usage_limit' => 100,
            'is_active' => true,
            'priority' => 15,
        ]);
        
        $frozen = $products->filter(function($p) {
            return str_contains(strtolower($p->name), 'frozen') || 
                   str_contains(strtolower($p->name), 'ice cream') || 
                   str_contains(strtolower($p->name), 'pizza') || 
                   str_contains(strtolower($p->name), 'fish finger');
        });
        $flashOffer->products()->attach($frozen->pluck('id'));

        // 6. Buy X Get Y at Discount - Bakery
        $bxgyDiscountOffer = Offer::create([
            'shop_id' => $shop->id,
            'name' => 'Bakery Special',
            'slug' => 'bakery-special',
            'description' => 'Buy 2 bakery items, get 50% off the third',
            'type' => 'bxgy_discount',
            'buy_quantity' => 2,
            'get_quantity' => 1,
            'get_discount_percentage' => 50,
            'starts_at' => Carbon::now(),
            'ends_at' => Carbon::now()->addDays(10),
            'badge_text' => 'HALF PRICE',
            'badge_color' => '#10B981',
            'is_active' => true,
            'priority' => 6,
        ]);
        
        $bakery = $products->filter(function($p) {
            return str_contains(strtolower($p->name), 'bread') || 
                   str_contains(strtolower($p->name), 'croissant') || 
                   str_contains(strtolower($p->name), 'bagel');
        });
        $bxgyDiscountOffer->products()->attach($bakery->pluck('id'));

        // 7. Bundle Deal - Breakfast Bundle
        $bundleOffer = Offer::create([
            'shop_id' => $shop->id,
            'name' => 'Breakfast Bundle',
            'slug' => 'breakfast-bundle',
            'description' => 'Complete breakfast bundle at special price',
            'type' => 'bundle',
            'buy_quantity' => 3,
            'bundle_price' => 8.00,
            'starts_at' => Carbon::now(),
            'ends_at' => Carbon::now()->addDays(20),
            'badge_text' => 'BUNDLE',
            'badge_color' => '#8B5CF6',
            'is_active' => true,
            'priority' => 5,
        ]);
        
        $breakfast = $products->filter(function($p) {
            return str_contains(strtolower($p->name), 'bread') || 
                   str_contains(strtolower($p->name), 'milk') || 
                   str_contains(strtolower($p->name), 'orange juice');
        })->take(5);
        $bundleOffer->products()->attach($breakfast->pluck('id'));

        // 8. Weekend Special - 15% off vegetables
        $weekendOffer = Offer::create([
            'shop_id' => $shop->id,
            'name' => 'Weekend Veggie Deal',
            'slug' => 'weekend-veggie-deal',
            'description' => '15% off all fresh vegetables this weekend',
            'type' => 'percentage_discount',
            'discount_value' => 15,
            'starts_at' => Carbon::now()->startOfWeek()->addDays(5), // Saturday
            'ends_at' => Carbon::now()->startOfWeek()->addDays(7), // Monday
            'badge_text' => '15% OFF',
            'badge_color' => '#22C55E',
            'is_active' => true,
            'priority' => 4,
        ]);
        
        $vegetables = $products->filter(function($p) {
            return str_contains(strtolower($p->name), 'tomato') || 
                   str_contains(strtolower($p->name), 'potato') || 
                   str_contains(strtolower($p->name), 'carrot') || 
                   str_contains(strtolower($p->name), 'onion') || 
                   str_contains(strtolower($p->name), 'pepper');
        });
        $weekendOffer->products()->attach($vegetables->pluck('id'));

        // 9. Expired offer (for testing filters)
        $expiredOffer = Offer::create([
            'shop_id' => $shop->id,
            'name' => 'Past Promotion',
            'slug' => 'past-promotion',
            'description' => 'This offer has expired',
            'type' => 'percentage_discount',
            'discount_value' => 25,
            'starts_at' => Carbon::now()->subDays(10),
            'ends_at' => Carbon::now()->subDays(3),
            'badge_text' => 'EXPIRED',
            'badge_color' => '#6B7280',
            'is_active' => false,
            'priority' => 1,
        ]);
        
        $expiredOffer->products()->attach($products->random(5)->pluck('id'));

        // 10. Inactive offer (for testing)
        $inactiveOffer = Offer::create([
            'shop_id' => $shop->id,
            'name' => 'Upcoming Summer Sale',
            'slug' => 'upcoming-summer-sale',
            'description' => 'Coming soon - not yet active',
            'type' => 'percentage_discount',
            'discount_value' => 35,
            'starts_at' => Carbon::now()->addDays(5),
            'ends_at' => Carbon::now()->addDays(15),
            'badge_text' => 'COMING SOON',
            'badge_color' => '#3B82F6',
            'is_active' => false,
            'priority' => 2,
        ]);
        
        $inactiveOffer->products()->attach($products->random(8)->pluck('id'));

        $this->command->info('Created 10 offers with various types and configurations.');
        $this->command->info('Active offers: 8');
        $this->command->info('Inactive/Expired: 2');
    }
}
