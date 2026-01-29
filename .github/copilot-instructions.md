# GitHub Copilot Instructions for ABC Grocery Shop

## Project Overview
ABC Grocery Shop is a multi-tenant Laravel 12 e-commerce platform for online grocery stores. The application uses **database-driven multi-tenancy** allowing a single deployment to serve unlimited shops with complete data isolation. Shops are identified by their domain name or slug, enabling easy scaling.

## Multi-Shop Database Architecture

This application uses **database-driven multi-tenancy** where each shop's data is completely isolated using `shop_id` foreign keys.

### Shop Model: `app/Models/Shop.php`
The master table that stores all configuration for each shop:
- Shop identity: name, slug, domain
- Location: address line 1, city, postcode, country
- Contact: phone, email
- Configuration: currency, currency symbol, delivery settings
- Features: is_active flag for enabling/disabling shops
- Branding: logo URL, colors (stored as JSON)
- Settings: operating hours, social links (stored as JSON)

### Multi-Tenancy Architecture
**Shop Detection Flow:**
1. `DetectShop` middleware runs on every request (added globally in `bootstrap/app.php`)
2. Middleware looks up shop by:
   - **Domain/Host header** (primary) - used in production with domain-based routing
   - **?shop=slug** query parameter (fallback) - used in development
   - **First active shop** (default) - if neither found
3. `ShopContext` service stores current shop in request context with caching
4. All database queries automatically scoped to `shop_id`
5. All record creation auto-assigns `shop_id` from ShopContext

### Tenant Tables
The following tables have `shop_id` foreign keys for complete data isolation:
- `users` - customers and admins
- `products` - shop's product catalog
- `product_variations` - product pricing and stock
- `categories` - product categories
- `orders` - customer orders
- `order_items` - order details
- `addresses` - customer delivery addresses
- `delivery_slots` - available delivery time slots
- `otps` - phone verification codes

### Using Shop Configuration in Code

**Service Class:**
```php
use App\Services\ShopConfigService;

$shopConfig = app(ShopConfigService::class);
$name = $shopConfig->name();
$phone = $shopConfig->phone();
$address = $shopConfig->fullAddress();
$currency = $shopConfig->currency();
```

**Get Current Shop ID:**
```php
use App\Services\ShopContext;

$shopId = ShopContext::getShopId();
$shop = ShopContext::getShop();
```

**In Views:**
```blade
{{ app(\App\Services\ShopConfigService::class)->name() }}
{{ app(\App\Services\ShopConfigService::class)->description() }}
```

**In Controllers:**
```php
public function __construct(ShopConfigService $shopConfig)
{
    $this->shopConfig = $shopConfig;
}
```

### Setting Up a New Shop
**Via Database Seeding:**
```php
// database/seeders/DatabaseSeeder.php
$shop = Shop::create([
    'name' => 'My Grocery Store',
    'slug' => 'my-grocery-store',
    'domain' => 'myshop.example.com',
    'description' => 'Fresh groceries online',
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

**Via Admin API:**
```
POST /api/admin/shops
{
    "name": "My Grocery Store",
    "slug": "my-grocery-store",
    "domain": "myshop.example.com",
    "description": "Fresh groceries online",
    "city": "London",
    "postcode": "SW1A 1AA",
    "country": "United Kingdom",
    "phone": "+44123456789",
    "email": "shop@example.com",
    "currency": "GBP",
    "currency_symbol": "£"
}
```

### Important Multi-Tenancy Rules
- **Never hardcode shop data** - always use `ShopConfigService`
- **All queries must filter by shop_id** - use `where('shop_id', $shopId)` in custom queries
- **All records must include shop_id** - auto-assigned from ShopContext when creating records
- **Shop detection happens automatically** - DetectShop middleware runs on every request
- **Feature flags** in Shop model control feature availability (check `is_feature_enabled()` method)
- **Domain-based routing** is preferred in production (setup DNS records to point to your app)
- **Query parameter fallback** (?shop=slug) works in development/testing
4. Clear Laravel cache: `php artisan config:clear`
5. Optionally update branding colors and logos



## Technology Stack
- **Framework**: Laravel 12 (PHP 8.2+)
- **Database**: SQLite (configurable to MySQL/PostgreSQL)
- **Authentication**: Laravel Sanctum with phone-based OTP
- **File Storage**: AWS S3
- **Email**: AWS SES
- **SMS**: Twilio
- **Frontend**: Blade templates with Tailwind CSS
- **Build Tool**: Vite

## Architecture Principles

### 1. Authentication & Authorization
- **Phone-based authentication only** - no passwords
- OTP verification via Twilio SMS
- Two user roles: `customer` and `admin`
- Use Sanctum tokens for API authentication
- Middleware: `admin` for admin routes, `customer` for customer-specific routes

### 2. API Design
- RESTful API structure
- Prefix: `/api` for all API endpoints
- Admin routes: `/api/admin/*`
- Always return JSON responses with proper HTTP status codes
- Use `Validator` facade for request validation
- Return validation errors with 422 status

### 3. Database Conventions
- Use migrations for all schema changes (in `database/migrations/`)
- Soft deletes for `users`, `products`, and `orders`
- Foreign key constraints with appropriate `onDelete` actions
- Index frequently queried columns
- Use Eloquent relationships over raw queries

### 4. Models & Relationships
- All models in `app/Models/`
- Define relationships explicitly: `hasMany`, `belongsTo`, `belongsToMany`
- Use accessor methods for computed properties (e.g., `getFullAddressAttribute`)
- Scope queries for common filters (e.g., `scopeActive`, `scopeFeatured`)
- Cast dates, booleans, and decimals appropriately

### 5. Product Management
- Products can have multiple variations (size, weight, price)
- Products belong to multiple categories (many-to-many)
- Product types: `standard`, `meat`, `frozen`, `fresh`, `perishable`
- Meat products have `is_halal`, `meat_type`, and `cut_type` fields
- All product images stored on AWS S3
- **Halal features** should only be visible if `shop.json` has `has_halal_products: true`

### 6. Order Management
- Order statuses: `pending`, `confirmed`, `processing`, `ready`, `out_for_delivery`, `delivered`, `completed`, `cancelled`, `refunded`
- Payment statuses: `pending`, `paid`, `failed`, `refunded`
- Payment methods: `cash`, `card`, `online`
- Fulfillment types: `delivery` or `collection`
- Always use database transactions for order creation/cancellation
- Restore stock on order cancellation

### 7. File Storage
- All file uploads go to AWS S3
- Use `Storage::disk('s3')` for S3 operations
- Store both `path` (S3 path) and `url` (full URL) in database
- Images organized by folders: `products/{product_id}/`

### 8. Controllers
- Separate admin and public controllers
- Admin controllers in `app/Http/Controllers/Admin/`
- Public API controllers in `app/Http/Controllers/Api/`
- Keep controllers thin - move business logic to Services
- Always handle exceptions with try-catch in critical operations

### 9. Services Layer
- Complex business logic in `app/Services/`
- Example: `OtpService` handles OTP generation and verification
- Services are dependency-injected into controllers
- Services should be stateless

### 10. Configuration
- **Shop-specific settings** in `config/shop.json` (name, branding, features, etc.)
- **Service credentials** in `.env` (AWS, Twilio, database)
- Shop defaults in `config/services.php` under `shop`, `delivery`, `otp` keys
- **Always prefer** `ShopConfigService` over hardcoded values
- Delivery configuration can be in shop.json or .env (shop.json takes precedence)

## Coding Standards

### Naming Conventions
- Controllers: `PascalCase` + `Controller` suffix
- Models: `PascalCase` (singular)
- Database tables: `snake_case` (plural)
- Routes: `kebab-case`
- Variables: `camelCase`
- Constants: `UPPER_SNAKE_CASE`

### Route Definitions
```php
// Public routes (no auth)
Route::get('/products', [ProductController::class, 'index']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    // customer routes
});

// Admin routes
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    // admin routes
});
```

### Validation Pattern
```php
$validator = Validator::make($request->all(), [
    'field' => 'required|string|max:255',
]);

if ($validator->fails()) {
    return response()->json(['errors' => $validator->errors()], 422);
}
```

### Database Transactions
```php
DB::beginTransaction();
try {
    // operations
    DB::commit();
    return response()->json($data, 201);
} catch (\Exception $e) {
    DB::rollBack();
    return response()->json(['message' => 'Error', 'error' => $e->getMessage()], 500);
}
```

### Model Scopes
```php
public function scopeActive($query)
{
    return $query->where('is_active', true);
}

// Usage: Product::active()->get();
```

## Key Features Implementation

### OTP Authentication Flow
1. User submits phone number → `POST /api/auth/send-otp`
2. System generates 6-digit OTP, stores in database with expiry
3. OTP sent via Twilio (or logged if no credentials)
4. User submits phone + OTP → `POST /api/auth/verify-otp`
5. System verifies OTP, creates/updates user, returns Sanctum token

### Order Creation Flow
1. Validate request (items, quantities, delivery slot, address)
2. Begin transaction
3. Check stock availability for all items
4. Calculate subtotal, delivery fee, total
5. Create order record
6. Create order items (snapshot of product data)
7. Reduce stock quantities
8. Increment delivery slot counter
9. Commit transaction

### Product Variations
- Each product can have multiple variations
- Variations define size, price, stock separately
- One variation marked as `is_default`
- Stock tracking at variation level, not product level

### Delivery Slots
- Define available time slots for delivery/collection
- Each slot has `max_orders` capacity
- Track `current_orders` to prevent overbooking
- Generate multiple slots at once for date ranges
- Slots must be in the future

## Common Patterns

### Image Upload
```php
$file = $request->file('image');
$path = Storage::disk('s3')->put('products/' . $product->id, $file, 'public');
$url = Storage::disk('s3')->url($path);
```

### Eager Loading
Always eager load relationships to avoid N+1 queries:
```php
Product::with(['variations', 'primaryImage', 'categories'])->get();
Order::with(['user', 'items.product', 'deliverySlot', 'address'])->find($id);
```

### Pagination
Use pagination for list endpoints:
```php
$products = Product::paginate($request->get('per_page', 20));
```

## Testing Guidelines
- Write feature tests for all API endpoints
- Test authentication flows thoroughly
- Test order creation with various scenarios (stock issues, slot availability)
- Mock Twilio and AWS services in tests
- Use in-memory SQLite for testing

## Environment Variables Required
```
AWS_ACCESS_KEY_ID
AWS_SECRET_ACCESS_KEY
AWS_DEFAULT_REGION
AWS_BUCKET
TWILIO_SID
TWILIO_TOKEN
TWILIO_FROM
SHOP_PHONE
SHOP_EMAIL
DELIVERY_FEE
FREE_DELIVERY_THRESHOLD
MIN_ORDER_AMOUNT
```

##**Never hardcode shop names or details** - always use `ShopConfigService`
- Never expose Twilio/AWS credentials in responses
- Always validate user ownership before modifying resources
- Use soft deletes for important data (users, products, orders)
- Log OTP codes to Laravel log in development (when Twilio not configured)
- Store order item details as snapshots (product name, price at time of order)
- Delivery slots must have `date >= today` for availability
- Admin endpoints must be protected with `admin` middleware
- Stock management is critical - always use transactions
- **Feature flags** in shop.json control which features are visible/enabled
- When creating new features, check if they should be feature-flaggedare
- Stock management is critical - always use transactions

## Future Enhancements Consideration
- Payment gateway integration (Stripe, PayPal)
- Real-time order tracking
- Customer reviews and ratings
- Loyalty points system
- Multi-language support
- Push notifications
- Analytics dashboard
- Inventory alerts
- BaShop Configuration**: `config/shop.json` (edit this to rebrand the shop)
- **Migrations**: `database/migrations/`
- **Models**: `app/Models/`
- **Controllers**: `app/Http/Controllers/{Api,Admin}/`
- **Middleware**: `app/Http/Middleware/`
- **Services**: `app/Services/` (includes `ShopConfigService`)
- **Controllers**: `app/Http/Controllers/{Api,Admin}/`
- **Middleware**: `app/Http/Middleware/`
- **Services**: `app/Services/`
- **Routes**: `routes/{api,web,console}.php`
- **Config**: `config/`
- **Views**: `resources/views/`
- **Frontend Assets**: `resources/{css,js}/`

---
**Never hardcode shop-specific values** - use ShopConfigService
2. Follow the established patterns and conventions
3. Add appropriate validation and error handling
4. Use database transactions for critical operations
5. Keep controllers thin, use services for business logic
6. Test authentication and authorization thoroughly
7. **Check feature flags** before showing/enabling optional features
8. When adding shop-specific data, consider if it belongs in shop.json
9. Add appropriate validation and error handling
3. Use database transactions for critical operations
4. Keep controllers thin, use services for business logic
5. Test authentication and authorization thoroughly
6. Update this file when adding new patterns or architectural decisions