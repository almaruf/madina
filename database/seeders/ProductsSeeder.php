<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        $shop = Shop::first();
        
        if (!$shop) {
            $this->command->error('No shop found. Please run DatabaseSeeder first.');
            return;
        }

        // Get or create categories
        $categories = [
            'meat' => Category::firstOrCreate(
                ['shop_id' => $shop->id, 'slug' => 'meat'],
                ['name' => 'Meat', 'is_active' => true, 'is_featured' => true]
            ),
            'vegetables' => Category::firstOrCreate(
                ['shop_id' => $shop->id, 'slug' => 'vegetables'],
                ['name' => 'Vegetables', 'is_active' => true, 'is_featured' => true]
            ),
            'fruits' => Category::firstOrCreate(
                ['shop_id' => $shop->id, 'slug' => 'fruits'],
                ['name' => 'Fruits', 'is_active' => true, 'is_featured' => true]
            ),
            'dairy' => Category::firstOrCreate(
                ['shop_id' => $shop->id, 'slug' => 'dairy'],
                ['name' => 'Dairy', 'is_active' => true, 'is_featured' => true]
            ),
            'bakery' => Category::firstOrCreate(
                ['shop_id' => $shop->id, 'slug' => 'bakery'],
                ['name' => 'Bakery', 'is_active' => true]
            ),
            'beverages' => Category::firstOrCreate(
                ['shop_id' => $shop->id, 'slug' => 'beverages'],
                ['name' => 'Beverages', 'is_active' => true]
            ),
            'snacks' => Category::firstOrCreate(
                ['shop_id' => $shop->id, 'slug' => 'snacks'],
                ['name' => 'Snacks', 'is_active' => true]
            ),
            'frozen' => Category::firstOrCreate(
                ['shop_id' => $shop->id, 'slug' => 'frozen'],
                ['name' => 'Frozen Foods', 'is_active' => true]
            ),
        ];

        $products = [
            // Meat Products
            [
                'name' => 'Chicken Breast',
                'slug' => 'chicken-breast',
                'description' => 'Fresh, skinless chicken breast fillets',
                'category' => 'meat',
                'type' => 'meat',
                'is_halal' => true,
                'is_featured' => true,
                'image' => 'https://images.unsplash.com/photo-1604503468506-a8da13d82791?w=400',
                'variations' => [
                    ['name' => '500g', 'price' => 4.99, 'stock_quantity' => 50],
                    ['name' => '1kg', 'price' => 8.99, 'stock_quantity' => 30],
                ]
            ],
            [
                'name' => 'Beef Mince',
                'slug' => 'beef-mince',
                'description' => 'Premium lean beef mince',
                'category' => 'meat',
                'type' => 'meat',
                'is_halal' => true,
                'is_featured' => true,
                'image' => 'https://images.unsplash.com/photo-1603048297172-c92544798d5a?w=400',
                'variations' => [
                    ['name' => '500g', 'price' => 5.99, 'stock_quantity' => 40],
                    ['name' => '1kg', 'price' => 10.99, 'stock_quantity' => 25],
                ]
            ],
            [
                'name' => 'Lamb Chops',
                'slug' => 'lamb-chops',
                'description' => 'Tender lamb chops, perfect for grilling',
                'category' => 'meat',
                'type' => 'meat',
                'is_halal' => true,
                'image' => 'https://images.unsplash.com/photo-1588347818036-4c0652d26e8e?w=400',
                'variations' => [
                    ['name' => '500g', 'price' => 9.99, 'stock_quantity' => 20],
                ]
            ],
            
            // Vegetables
            [
                'name' => 'Tomatoes',
                'slug' => 'tomatoes',
                'description' => 'Fresh, ripe tomatoes',
                'category' => 'vegetables',
                'type' => 'fresh',
                'is_featured' => true,
                'image' => 'https://images.unsplash.com/photo-1546470427-227dadf2563c?w=400',
                'variations' => [
                    ['name' => '500g', 'price' => 1.49, 'stock_quantity' => 100],
                    ['name' => '1kg', 'price' => 2.49, 'stock_quantity' => 80],
                ]
            ],
            [
                'name' => 'Potatoes',
                'slug' => 'potatoes',
                'description' => 'White potatoes, perfect for roasting',
                'category' => 'vegetables',
                'type' => 'fresh',
                'image' => 'https://images.unsplash.com/photo-1518977676601-b53f82aba655?w=400',
                'variations' => [
                    ['name' => '2kg', 'price' => 2.99, 'stock_quantity' => 60],
                    ['name' => '5kg', 'price' => 5.99, 'stock_quantity' => 40],
                ]
            ],
            [
                'name' => 'Carrots',
                'slug' => 'carrots',
                'description' => 'Fresh organic carrots',
                'category' => 'vegetables',
                'type' => 'fresh',
                'image' => 'https://images.unsplash.com/photo-1598170845058-32b9d6a5da37?w=400',
                'variations' => [
                    ['name' => '1kg', 'price' => 1.29, 'stock_quantity' => 70],
                ]
            ],
            [
                'name' => 'Onions',
                'slug' => 'onions',
                'description' => 'Brown onions',
                'category' => 'vegetables',
                'type' => 'fresh',
                'image' => 'https://images.unsplash.com/photo-1508747703725-719777637510?w=400',
                'variations' => [
                    ['name' => '1kg', 'price' => 0.99, 'stock_quantity' => 90],
                ]
            ],
            [
                'name' => 'Bell Peppers',
                'slug' => 'bell-peppers',
                'description' => 'Mixed bell peppers',
                'category' => 'vegetables',
                'type' => 'fresh',
                'image' => 'https://images.unsplash.com/photo-1563565375-f3fdfdbefa83?w=400',
                'variations' => [
                    ['name' => '3 Pack', 'price' => 2.49, 'stock_quantity' => 50],
                ]
            ],
            
            // Fruits
            [
                'name' => 'Bananas',
                'slug' => 'bananas',
                'description' => 'Fresh yellow bananas',
                'category' => 'fruits',
                'type' => 'fresh',
                'is_featured' => true,
                'image' => 'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?w=400',
                'variations' => [
                    ['name' => '1kg', 'price' => 1.19, 'stock_quantity' => 120],
                ]
            ],
            [
                'name' => 'Apples',
                'slug' => 'apples',
                'description' => 'Crisp red apples',
                'category' => 'fruits',
                'type' => 'fresh',
                'is_featured' => true,
                'image' => 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?w=400',
                'variations' => [
                    ['name' => '6 Pack', 'price' => 2.99, 'stock_quantity' => 80],
                ]
            ],
            [
                'name' => 'Oranges',
                'slug' => 'oranges',
                'description' => 'Juicy oranges',
                'category' => 'fruits',
                'type' => 'fresh',
                'image' => 'https://images.unsplash.com/photo-1582979512210-99b6a53386f9?w=400',
                'variations' => [
                    ['name' => '1kg', 'price' => 2.49, 'stock_quantity' => 70],
                ]
            ],
            [
                'name' => 'Strawberries',
                'slug' => 'strawberries',
                'description' => 'Sweet fresh strawberries',
                'category' => 'fruits',
                'type' => 'fresh',
                'image' => 'https://images.unsplash.com/photo-1464965911861-746a04b4bca6?w=400',
                'variations' => [
                    ['name' => '400g', 'price' => 3.49, 'stock_quantity' => 40],
                ]
            ],
            
            // Dairy
            [
                'name' => 'Whole Milk',
                'slug' => 'whole-milk',
                'description' => 'Fresh whole milk',
                'category' => 'dairy',
                'type' => 'perishable',
                'is_featured' => true,
                'image' => 'https://images.unsplash.com/photo-1563636619-e9143da7973b?w=400',
                'variations' => [
                    ['name' => '2L', 'price' => 2.29, 'stock_quantity' => 60],
                    ['name' => '4L', 'price' => 3.99, 'stock_quantity' => 40],
                ]
            ],
            [
                'name' => 'Cheddar Cheese',
                'slug' => 'cheddar-cheese',
                'description' => 'Mature cheddar cheese',
                'category' => 'dairy',
                'type' => 'perishable',
                'image' => 'https://images.unsplash.com/photo-1618164436241-4473940d1f5c?w=400',
                'variations' => [
                    ['name' => '200g', 'price' => 2.99, 'stock_quantity' => 50],
                ]
            ],
            [
                'name' => 'Greek Yogurt',
                'slug' => 'greek-yogurt',
                'description' => 'Creamy Greek yogurt',
                'category' => 'dairy',
                'type' => 'perishable',
                'image' => 'https://images.unsplash.com/photo-1488477181946-6428a0291777?w=400',
                'variations' => [
                    ['name' => '500g', 'price' => 3.49, 'stock_quantity' => 40],
                ]
            ],
            [
                'name' => 'Butter',
                'slug' => 'butter',
                'description' => 'Salted butter',
                'category' => 'dairy',
                'type' => 'perishable',
                'image' => 'https://images.unsplash.com/photo-1589985270826-4b7bb135bc9d?w=400',
                'variations' => [
                    ['name' => '250g', 'price' => 1.99, 'stock_quantity' => 70],
                ]
            ],
            
            // Bakery
            [
                'name' => 'White Bread',
                'slug' => 'white-bread',
                'description' => 'Fresh white sliced bread',
                'category' => 'bakery',
                'type' => 'perishable',
                'is_featured' => true,
                'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400',
                'variations' => [
                    ['name' => '800g', 'price' => 1.29, 'stock_quantity' => 80],
                ]
            ],
            [
                'name' => 'Croissants',
                'slug' => 'croissants',
                'description' => 'Butter croissants',
                'category' => 'bakery',
                'type' => 'perishable',
                'image' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=400',
                'variations' => [
                    ['name' => '6 Pack', 'price' => 3.99, 'stock_quantity' => 30],
                ]
            ],
            [
                'name' => 'Bagels',
                'slug' => 'bagels',
                'description' => 'Plain bagels',
                'category' => 'bakery',
                'type' => 'standard',
                'image' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=400',
                'variations' => [
                    ['name' => '4 Pack', 'price' => 2.49, 'stock_quantity' => 40],
                ]
            ],
            
            // Beverages
            [
                'name' => 'Orange Juice',
                'slug' => 'orange-juice',
                'description' => 'Fresh orange juice',
                'category' => 'beverages',
                'type' => 'perishable',
                'image' => 'https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=400',
                'variations' => [
                    ['name' => '1L', 'price' => 2.99, 'stock_quantity' => 50],
                ]
            ],
            [
                'name' => 'Sparkling Water',
                'slug' => 'sparkling-water',
                'description' => 'Carbonated mineral water',
                'category' => 'beverages',
                'type' => 'standard',
                'image' => 'https://images.unsplash.com/photo-1523362628745-0c100150b504?w=400',
                'variations' => [
                    ['name' => '1.5L', 'price' => 0.99, 'stock_quantity' => 100],
                ]
            ],
            [
                'name' => 'Green Tea',
                'slug' => 'green-tea',
                'description' => 'Premium green tea bags',
                'category' => 'beverages',
                'type' => 'standard',
                'image' => 'https://images.unsplash.com/photo-1564890369478-c89ca6d9cde9?w=400',
                'variations' => [
                    ['name' => '20 Bags', 'price' => 3.49, 'stock_quantity' => 60],
                ]
            ],
            
            // Snacks
            [
                'name' => 'Potato Chips',
                'slug' => 'potato-chips',
                'description' => 'Salted potato chips',
                'category' => 'snacks',
                'type' => 'standard',
                'image' => 'https://images.unsplash.com/photo-1566478989037-eec170784d0b?w=400',
                'variations' => [
                    ['name' => '150g', 'price' => 1.99, 'stock_quantity' => 80],
                ]
            ],
            [
                'name' => 'Chocolate Bar',
                'slug' => 'chocolate-bar',
                'description' => 'Milk chocolate bar',
                'category' => 'snacks',
                'type' => 'standard',
                'image' => 'https://images.unsplash.com/photo-1511381939415-e44015466834?w=400',
                'variations' => [
                    ['name' => '100g', 'price' => 1.49, 'stock_quantity' => 120],
                ]
            ],
            [
                'name' => 'Mixed Nuts',
                'slug' => 'mixed-nuts',
                'description' => 'Roasted mixed nuts',
                'category' => 'snacks',
                'type' => 'standard',
                'image' => 'https://images.unsplash.com/photo-1599599810769-bcde5a160d32?w=400',
                'variations' => [
                    ['name' => '200g', 'price' => 4.49, 'stock_quantity' => 50],
                ]
            ],
            
            // Frozen Foods
            [
                'name' => 'Frozen Peas',
                'slug' => 'frozen-peas',
                'description' => 'Garden peas',
                'category' => 'frozen',
                'type' => 'frozen',
                'image' => 'https://images.unsplash.com/photo-1618897996318-5a901fa6ca71?w=400',
                'variations' => [
                    ['name' => '1kg', 'price' => 2.49, 'stock_quantity' => 60],
                ]
            ],
            [
                'name' => 'Ice Cream',
                'slug' => 'ice-cream',
                'description' => 'Vanilla ice cream',
                'category' => 'frozen',
                'type' => 'frozen',
                'is_featured' => true,
                'image' => 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=400',
                'variations' => [
                    ['name' => '1L', 'price' => 3.99, 'stock_quantity' => 40],
                ]
            ],
            [
                'name' => 'Frozen Pizza',
                'slug' => 'frozen-pizza',
                'description' => 'Margherita pizza',
                'category' => 'frozen',
                'type' => 'frozen',
                'image' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=400',
                'variations' => [
                    ['name' => '400g', 'price' => 4.99, 'stock_quantity' => 35],
                ]
            ],
            [
                'name' => 'Fish Fingers',
                'slug' => 'fish-fingers',
                'description' => 'Breaded fish fingers',
                'category' => 'frozen',
                'type' => 'frozen',
                'image' => 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?w=400',
                'variations' => [
                    ['name' => '300g', 'price' => 3.49, 'stock_quantity' => 45],
                ]
            ],
        ];

        foreach ($products as $productData) {
            // Skip if product already exists
            if (Product::where('shop_id', $shop->id)->where('slug', $productData['slug'])->exists()) {
                $this->command->warn('Skipping existing product: ' . $productData['name']);
                continue;
            }

            $product = Product::create([
                'shop_id' => $shop->id,
                'name' => $productData['name'],
                'slug' => $productData['slug'],
                'description' => $productData['description'],
                'type' => $productData['type'],
                'is_halal' => $productData['is_halal'] ?? false,
                'is_featured' => $productData['is_featured'] ?? false,
                'is_active' => true,
            ]);

            // Attach category
            $product->categories()->attach($categories[$productData['category']]->id);

            // Add variations
            foreach ($productData['variations'] as $index => $variation) {
                ProductVariation::create([
                    'product_id' => $product->id,
                    'name' => $variation['name'],
                    'price' => $variation['price'],
                    'stock_quantity' => $variation['stock_quantity'],
                    'is_default' => $index === 0,
                ]);
            }

            // Add image
            if (isset($productData['image'])) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $productData['image'],
                    'url' => $productData['image'],
                    'is_primary' => true,
                ]);
            }
        }

        $this->command->info('Created ' . count($products) . ' products with variations and images.');
    }
}
