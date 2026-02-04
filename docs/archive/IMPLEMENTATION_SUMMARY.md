# Multi-Tenancy Implementation - Quick Reference

## What Was Completed

✅ **Complete database-driven multi-tenancy system** enabling a single deployment to serve unlimited shops with complete data isolation.

## Key Components

### 1. Shop Model (`app/Models/Shop.php`)
- Master tenant configuration table
- Stores: name, slug, domain, location, contact, currency, settings, branding
- Has relationships to all tenant data: users, products, categories, orders, addresses, delivery_slots

### 2. Database Schema
- **shops table** - Master tenant configuration
- All tenant tables have `shop_id` foreign key: users, products, product_variations, categories, orders, order_items, addresses, delivery_slots, otps

### 3. Shop Detection (`app/Http/Middleware/DetectShop.php`)
- Runs globally on every request
- Detects shop by:
  1. Domain/host header (production)
  2. `?shop=slug` query parameter (development)
  3. First active shop (default)

### 4. Shop Context (`app/Services/ShopContext.php`)
- Maintains current shop in request context
- Methods: `getShop()`, `getShopId()`, `findByDomain()`, `findBySlug()`
- Caches for 1 hour for performance
- Automatically cleared when shop is updated

### 5. Shop Config Service (Updated `app/Services/ShopConfigService.php`)
- Now database-driven instead of JSON-based
- Provides access to current shop configuration
- Methods: `name()`, `phone()`, `email()`, `currency()`, `fullAddress()`, etc.
- Usage: `app(ShopConfigService::class)->name()`

### 6. Shop Management (`app/Http/Controllers/Admin/ShopController.php`)
- Admin endpoints for shop CRUD
- Routes: `/api/admin/shops` (index, store, show, update, destroy)
- Additional: `GET /api/admin/shops/current`, `PATCH /api/admin/shops/current`

### 7. All Models Updated
- User, Product, Category, Order, Address, DeliverySlot, Otp
- All have: `shop_id` in fillable, `shop()` relationship method

### 8. All Controllers Updated
- All API controllers filter queries by `shop_id`
- All admin controllers filter by `shop_id`
- All create operations auto-assign `shop_id` from ShopContext

### 9. OTP Service Updated (`app/Services/OtpService.php`)
- Now uses current shop name in SMS messages
- Gets shop context for personalized communications

## File Structure

```
/workspaces/madina/
├── app/
│   ├── Models/
│   │   ├── Shop.php (NEW)
│   │   ├── User.php (UPDATED)
│   │   ├── Product.php (UPDATED)
│   │   ├── Category.php (UPDATED)
│   │   ├── Order.php (UPDATED)
│   │   ├── Address.php (UPDATED)
│   │   ├── DeliverySlot.php (UPDATED)
│   │   └── Otp.php (UPDATED)
│   ├── Services/
│   │   ├── ShopContext.php (NEW)
│   │   ├── ShopConfigService.php (UPDATED)
│   │   └── OtpService.php (UPDATED)
│   ├── Http/
│   │   ├── Middleware/
│   │   │   └── DetectShop.php (NEW)
│   │   └── Controllers/
│   │       ├── Admin/
│   │       │   ├── ShopController.php (NEW)
│   │       │   ├── ProductController.php (UPDATED)
│   │       │   ├── OrderController.php (UPDATED)
│   │       │   ├── DeliverySlotController.php (UPDATED)
│   │       │   └── CategoryController.php (UPDATED)
│   │       └── Api/
│   │           ├── AuthController.php (UPDATED)
│   │           ├── ProductController.php (UPDATED)
│   │           ├── OrderController.php (UPDATED)
│   │           ├── AddressController.php (UPDATED)
│   │           └── DeliverySlotController.php (UPDATED)
├── bootstrap/
│   └── app.php (UPDATED - DetectShop middleware registered)
├── database/
│   ├── migrations/
│   │   ├── create_shops_table.php (NEW)
│   │   ├── add_shop_id_to_tables.php (NEW)
│   │   └── (other migrations)
│   └── seeders/
│       └── DatabaseSeeder.php (UPDATED - creates first shop)
├── routes/
│   ├── api.php (UPDATED - added /api/admin/shops routes)
│   └── api.shop.php (NEW - shop routes reference)
├── .github/
│   └── copilot-instructions.md (UPDATED - multi-tenancy docs)
├── README.md (UPDATED - multi-tenancy setup)
└── MULTI_TENANCY.md (NEW - detailed architecture guide)
```

## How to Use

### Development - Access Shops

```bash
# Admin shop
http://localhost:8000/?shop=abc-grocery

# For specific admin/customer:
# Login with phone: +441234567890 (admin)
# Login with phone: +441234567891 (customer)
```

### Create New Shop (Development)

```bash
curl -X POST http://localhost:8000/api/admin/shops \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "New Store",
    "slug": "new-store",
    "domain": "newstore.example.com",
    "description": "Description here",
    "address_line_1": "123 Main St",
    "city": "London",
    "postcode": "SW1A 1AA",
    "country": "United Kingdom",
    "phone": "+441234567890",
    "email": "shop@example.com",
    "currency": "GBP",
    "currency_symbol": "£"
  }'
```

### Access Different Shop (Development)

```bash
# Change shop parameter
http://localhost:8000/?shop=new-store
```

### Production - Domain-Based

Each shop gets its own domain:
- shop1.example.com → Shop1
- shop2.example.com → Shop2
- shop3.example.com → Shop3

Domain is automatically detected, no query parameters needed.

## Code Patterns

### In Controllers
```php
use App\Services\ShopContext;

$shopId = ShopContext::getShopId();
$products = Product::where('shop_id', $shopId)
    ->where('is_active', true)
    ->get();
```

### In Services
```php
use App\Services\ShopContext;

$shop = ShopContext::getShop();
$currency = $shop->currency;
$email = $shop->email;
```

### In Views
```blade
{{ app(\App\Services\ShopConfigService::class)->name() }}
{{ app(\App\Services\ShopConfigService::class)->currency() }}
```

## Important Rules

1. ✅ **Always use ShopContext** - Never hardcode shop data
2. ✅ **Always filter by shop_id** - Every query must include it
3. ✅ **Always assign shop_id on creation** - Use ShopContext::getShopId()
4. ✅ **Use relationships** - Prefer `$shop->products()` over raw queries
5. ✅ **Shop detection is automatic** - Middleware handles it globally

## API Endpoints

### Shop Management (Admin Only)
```
GET    /api/admin/shops                    # List shops
POST   /api/admin/shops                    # Create shop
GET    /api/admin/shops/{id}               # Get shop
PATCH  /api/admin/shops/{id}               # Update shop
DELETE /api/admin/shops/{id}               # Delete shop
GET    /api/admin/shops/current            # Current shop
PATCH  /api/admin/shops/current            # Update current
```

### Shop-Specific Endpoints (Auto-filtered)
```
GET    /api/admin/products                 # Shop products only
GET    /api/admin/orders                   # Shop orders only
GET    /api/admin/delivery-slots           # Shop slots only
GET    /api/admin/categories               # Shop categories only
GET    /api/products                       # Shop products (public)
GET    /api/orders                         # User's orders (authenticated)
```

## Documentation

- **Architecture Details**: See [MULTI_TENANCY.md](MULTI_TENANCY.md)
- **Development Instructions**: See [.github/copilot-instructions.md](.github/copilot-instructions.md)
- **Quick Start**: See [README.md](README.md#getting-started---multi-tenant-setup)

## Testing

Run database seeder to create demo data:
```bash
php artisan migrate
php artisan db:seed
```

This creates:
- Shop: "ABC Grocery Shop" (slug: abc-grocery)
- Admin user: +441234567890
- Customer user: +441234567891
- Sample categories and products

Then access at: `http://localhost:8000/?shop=abc-grocery`

## Next Steps for Deployment

1. **Set up DNS**: Point shop domains to your server
   - shop1.example.com → your-server
   - shop2.example.com → your-server
   - etc.

2. **Configure Certificates**: Get SSL certificates for each domain (or wildcard)

3. **Deploy**: Standard Laravel deployment with database migrations

4. **Create Shops**: Via admin API or database seeding

5. **Shop Management**: Access admin panel via shop domain + /api/admin/shops

## Troubleshooting

**Shop not detected:**
- Ensure shop exists in database
- In dev, check query parameter: `?shop=slug-value`
- Verify shop is_active = true

**Data from wrong shop:**
- Check all queries include `where('shop_id', $shopId)`
- Verify shop_id is assigned on creation

**Admin API returns 404:**
- Verify authenticated user has `role = 'admin'`
- Confirm current shop exists in database
