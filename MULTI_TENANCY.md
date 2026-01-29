# Database-Driven Multi-Tenancy Architecture

This document explains how the multi-tenancy system works in ABC Grocery Shop.

## Overview

ABC Grocery Shop uses **database-driven multi-tenancy** to serve unlimited shops from a single deployment. Each shop is stored in the database with complete data isolation via `shop_id` foreign keys on all tenant tables.

## Shop Detection Flow

When a request comes in, the `DetectShop` middleware automatically detects which shop is being accessed:

1. **Primary: Domain/Host Header** - Used in production
   - Shop is looked up by the request's host/domain
   - Each shop has its own domain (e.g., shop1.example.com, shop2.example.com)
   - Very secure and scalable for production deployments

2. **Fallback: Query Parameter** - Used in development
   - `?shop=slug` query parameter specifies the shop
   - Example: `http://localhost:8000/?shop=abc-grocery`
   - Useful for testing multiple shops during development

3. **Default: First Active Shop** - Fallback if neither above found
   - If no domain match and no query param, use the first active shop
   - Prevents 404 errors when testing

## Shop Context Service

The `ShopContext` service maintains the current shop in the request context:

```php
use App\Services\ShopContext;

// Get current shop ID
$shopId = ShopContext::getShopId();

// Get full shop object
$shop = ShopContext::getShop();

// Shop lookup with caching
$shopByDomain = ShopContext::findByDomain('shop.example.com');
$shopBySlug = ShopContext::findBySlug('my-shop');
```

Context is cached for 1 hour for performance.

## Database Schema

### Shops Table
Master table containing all shop configuration:

```php
Schema::create('shops', function (Blueprint $table) {
    $table->id();
    $table->string('name');                    // Shop name
    $table->string('slug')->unique();          // URL slug
    $table->string('domain')->unique();        // Domain name
    $table->text('description')->nullable();
    $table->string('tagline')->nullable();
    $table->string('address_line_1')->nullable();
    $table->string('city')->nullable();
    $table->string('postcode')->nullable();
    $table->string('country')->nullable();
    $table->string('phone')->nullable();
    $table->string('email')->nullable();
    $table->string('currency')->default('GBP');
    $table->string('currency_symbol')->default('£');
    $table->boolean('is_active')->default(true);
    $table->json('settings')->nullable();      // Operating hours, social links, etc.
    $table->json('branding')->nullable();      // Colors, logo URLs, etc.
    $table->timestamps();
});
```

### Tenant Tables
All of these tables have `shop_id` foreign key for data isolation:

- **users** - Customers and admins
- **products** - Shop's product catalog
- **product_variations** - Product pricing and stock
- **categories** - Product categories
- **orders** - Customer orders
- **order_items** - Order line items
- **addresses** - Customer delivery addresses
- **delivery_slots** - Available delivery time slots
- **otps** - Phone verification codes

## Using Shop Context in Code

### In Controllers

```php
use App\Services\ShopContext;

class ProductController extends Controller
{
    public function index()
    {
        $shopId = ShopContext::getShopId();
        
        // All queries automatically filtered to current shop
        $products = Product::where('shop_id', $shopId)
            ->where('is_active', true)
            ->with(['variations', 'categories'])
            ->paginate();
            
        return response()->json($products);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:products',
            'type' => 'required|in:standard,meat,frozen,fresh',
        ]);
        
        // shop_id automatically assigned from ShopContext
        $product = Product::create([
            ...$validated,
            'shop_id' => ShopContext::getShopId(),
        ]);
        
        return response()->json($product, 201);
    }
}
```

### In Services

```php
use App\Services\ShopContext;
use App\Services\ShopConfigService;

class OrderService
{
    public function createOrder($items)
    {
        $shopId = ShopContext::getShopId();
        $shop = ShopContext::getShop();
        
        // Use shop data
        $currency = $shop->currency;
        $phone = $shop->phone;
        
        // Or use ShopConfigService as wrapper
        $config = app(ShopConfigService::class);
        $shopName = $config->name();
    }
}
```

### In Views (Blade)

```blade
<!-- Get current shop name -->
{{ app(\App\Services\ShopConfigService::class)->name() }}

<!-- Get current shop currency -->
{{ app(\App\Services\ShopConfigService::class)->currency() }}

<!-- Check if feature enabled -->
@if(auth()->user()->shop->is_feature_enabled('delivery'))
    <!-- Show delivery options -->
@endif
```

## Creating a New Shop

### Via Seeder

```php
use App\Models\Shop;

$shop = Shop::create([
    'name' => 'My Grocery Store',
    'slug' => 'my-store',
    'domain' => 'mystore.example.com',
    'description' => 'Fresh groceries delivered',
    'city' => 'London',
    'postcode' => 'SW1A 1AA',
    'country' => 'United Kingdom',
    'phone' => '+44123456789',
    'email' => 'shop@example.com',
    'currency' => 'GBP',
    'currency_symbol' => '£',
    'is_active' => true,
]);
```

### Via API

```bash
curl -X POST http://localhost:8000/api/admin/shops \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "My Grocery Store",
    "slug": "my-store",
    "domain": "mystore.example.com",
    "description": "Fresh groceries",
    "city": "London",
    "postcode": "SW1A 1AA",
    "country": "United Kingdom",
    "phone": "+44123456789",
    "email": "shop@example.com",
    "currency": "GBP",
    "currency_symbol": "£"
  }'
```

## Important Multi-Tenancy Rules

1. **Never Hardcode Shop Data**
   ```php
   // ❌ WRONG - Hardcoded
   return "Welcome to Madina Halal Shop";
   
   // ✅ CORRECT - Use service
   return "Welcome to " . app(ShopConfigService::class)->name();
   ```

2. **Always Filter by shop_id**
   ```php
   // ❌ WRONG - Missing shop_id filter
   $orders = Order::where('status', 'completed')->get();
   
   // ✅ CORRECT - Filter by shop_id
   $orders = Order::where('shop_id', ShopContext::getShopId())
       ->where('status', 'completed')
       ->get();
   ```

3. **Always Assign shop_id on Creation**
   ```php
   // ❌ WRONG - Missing shop_id
   $product = Product::create(['name' => 'Apple']);
   
   // ✅ CORRECT - Include shop_id
   $product = Product::create([
       'name' => 'Apple',
       'shop_id' => ShopContext::getShopId(),
   ]);
   ```

4. **Use Relationships for Queries**
   ```php
   // ❌ WRONG - Raw queries
   $users = DB::table('users')->get();
   
   // ✅ CORRECT - Use relationships
   $shop = ShopContext::getShop();
   $users = $shop->users()->get();
   ```

## Admin Endpoints

### Shop Management (Super Admin Only)

```
GET    /api/admin/shops                    # List all shops
POST   /api/admin/shops                    # Create shop
GET    /api/admin/shops/current            # Get current shop
PATCH  /api/admin/shops/current            # Update current shop
GET    /api/admin/shops/{id}               # View shop
PATCH  /api/admin/shops/{id}               # Update shop
DELETE /api/admin/shops/{id}               # Delete shop
```

### Shop-Specific Admin Endpoints

```
GET    /api/admin/products                 # List shop products only
GET    /api/admin/orders                   # List shop orders only
GET    /api/admin/delivery-slots           # List shop delivery slots
GET    /api/admin/categories               # List shop categories
```

## Development vs Production

### Development
- Use query parameter: `http://localhost:8000/?shop=abc-grocery`
- Test multiple shops easily
- Single domain (localhost)

### Production
- Set up DNS records: shop1.example.com, shop2.example.com, etc.
- Each shop accessed via its own domain
- No query parameters needed
- More secure and scalable

## Cache Management

ShopContext uses caching for performance:

```php
// Cache is automatically used for lookups
$shop = ShopContext::findByDomain('shop.example.com'); // Cached for 1 hour

// Clear cache when shop is updated
ShopContext::clearCache(); // Called automatically in ShopController

// Or manually clear
Cache::forget('shop:domain:shop.example.com');
```

## Troubleshooting

### "Shop not found" Error
1. Ensure shop domain/slug exists in database
2. In development, use correct query parameter: `?shop=slug`
3. Check shop's `is_active` flag is true

### Data from Wrong Shop
1. Verify all queries include `where('shop_id', $shopId)`
2. Check ShopContext is being used correctly
3. Ensure shop_id is assigned on record creation

### API Returning Empty Results
1. Verify records exist for current shop in database
2. Check query parameters: `?shop=slug` in development
3. Confirm authentication token matches shop's user

## File Locations

- **Shop Model**: [app/Models/Shop.php](app/Models/Shop.php)
- **ShopContext Service**: [app/Services/ShopContext.php](app/Services/ShopContext.php)
- **DetectShop Middleware**: [app/Http/Middleware/DetectShop.php](app/Http/Middleware/DetectShop.php)
- **ShopConfigService**: [app/Services/ShopConfigService.php](app/Services/ShopConfigService.php)
- **ShopController**: [app/Http/Controllers/Admin/ShopController.php](app/Http/Controllers/Admin/ShopController.php)
- **Migrations**: [database/migrations/](database/migrations/)
