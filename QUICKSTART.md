# Quick Start Guide - ABC Grocery Shop Multi-Tenant Platform

Get up and running with the multi-tenant grocery platform in 5 minutes.

## Prerequisites

- PHP 8.2+
- Composer
- Node.js & npm
- SQLite3 (or MySQL/PostgreSQL)

## Installation (5 minutes)

### 1. Install Dependencies
```bash
cd /workspaces/madina

# PHP dependencies
composer install

# Node dependencies
npm install
```

### 2. Environment Setup
```bash
# Copy example env
cp .env.example .env

# Generate app key
php artisan key:generate

# Create SQLite database
touch database/database.sqlite
```

### 3. Database Initialization
```bash
# Run migrations
php artisan migrate

# Seed demo shop and users
php artisan db:seed
```

### 4. Start Development Server
```bash
# Terminal 1: Start Laravel
php artisan serve

# Terminal 2: Start asset compilation
npm run dev
```

**Application ready at**: `http://localhost:8000`

## First Login (2 minutes)

### Demo Credentials
The seeder creates two demo users:

**Admin User**
- Phone: `+441234567890`
- Role: Admin (can manage shop settings, products, orders)

**Customer User**
- Phone: `+441234567891`
- Role: Customer (can browse products, place orders)

### Login Steps
1. Go to `http://localhost:8000/?shop=abc-grocery`
2. Click "Login with Phone"
3. Enter phone number (e.g., `+441234567890`)
4. System sends OTP (visible in Laravel log)
5. Enter OTP: `123456` (hardcoded in development)
6. Logged in!

## Accessing Different Shops

### During Development
Use the `?shop=` query parameter:

```
# ABC Grocery Shop
http://localhost:8000/?shop=abc-grocery

# Your new shop (after creating)
http://localhost:8000/?shop=my-store
```

### In Production
Each shop has its own domain:
```
# Shop 1
https://shop1.example.com

# Shop 2
https://shop2.example.com
```

## Common Tasks

### Create a New Shop

**Via API (Requires Admin Token)**
```bash
# Get admin token
PHONE="+441234567890"
curl -X POST http://localhost:8000/api/auth/send-otp \
  -H "Content-Type: application/json" \
  -d "{\"phone\": \"$PHONE\"}"

# Verify OTP and get token
curl -X POST http://localhost:8000/api/auth/verify-otp \
  -H "Content-Type: application/json" \
  -d "{\"phone\": \"$PHONE\", \"otp\": \"123456\"}"
# Copy the token from response

# Create shop
TOKEN="your_token_here"
curl -X POST http://localhost:8000/api/admin/shops \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "My Grocery Store",
    "slug": "my-store",
    "domain": "mystore.localhost",
    "description": "Fresh groceries",
    "address_line_1": "123 Main St",
    "city": "London",
    "postcode": "SW1A 1AA",
    "country": "United Kingdom",
    "phone": "+442071838750",
    "email": "shop@example.com",
    "currency": "GBP",
    "currency_symbol": "£"
  }'
```

**Via Database (Quick)**
```bash
php artisan tinker

# Create shop
>>> $shop = App\Models\Shop::create([
  'name' => 'My Shop',
  'slug' => 'my-shop',
  'domain' => 'myshop.localhost',
  'description' => 'Fresh groceries',
  'address_line_1' => '123 Main St',
  'city' => 'London',
  'postcode' => 'SW1A 1AA',
  'country' => 'United Kingdom',
  'phone' => '+441234567890',
  'email' => 'shop@example.com',
  'currency' => 'GBP',
  'currency_symbol' => '£',
  'is_active' => true,
]);

# Create admin for shop
>>> App\Models\User::create([
  'shop_id' => $shop->id,
  'phone' => '+441234567892',
  'name' => 'Shop Admin',
  'role' => 'admin',
  'phone_verified' => true,
  'phone_verified_at' => now(),
]);

>>> exit
```

### Add Products

1. Login as admin: `http://localhost:8000/?shop=abc-grocery`
2. Navigate to admin section
3. Go to Products → Add Product
4. Fill in product details
5. Add variations (size, price, stock)
6. Save

Or via API (see API reference below)

### View Orders

Login as admin, navigate to Orders section. Only see orders for current shop.

## File Structure

**Key Directories**
- `app/Models/` - Data models (Shop, Product, Order, etc.)
- `app/Services/` - Business logic (ShopContext, ShopConfigService, etc.)
- `app/Http/Controllers/` - Request handlers
- `database/migrations/` - Database schema
- `routes/` - API routes

**Key Files**
- `app/Http/Middleware/DetectShop.php` - Shop detection middleware
- `database/seeders/DatabaseSeeder.php` - Demo data
- `.github/copilot-instructions.md` - Architecture documentation
- `MULTI_TENANCY.md` - Detailed multi-tenancy guide

## API Reference

### Public Endpoints

**Authentication**
```bash
# Send OTP
POST /api/auth/send-otp
{ "phone": "+441234567890" }

# Verify OTP and login
POST /api/auth/verify-otp
{ "phone": "+441234567890", "otp": "123456" }

# Logout
POST /api/auth/logout
```

**Products** (No auth required)
```bash
# List products
GET /api/products

# Get product details
GET /api/products/{slug}

# List categories
GET /api/categories

# List delivery slots
GET /api/delivery-slots
```

### Authenticated Endpoints

**Orders**
```bash
# List my orders
GET /api/orders
Authorization: Bearer TOKEN

# Create order
POST /api/orders
Authorization: Bearer TOKEN
{
  "items": [{"product_variation_id": 1, "quantity": 2}],
  "delivery_slot_id": 1,
  "address_id": 1,
  "payment_method": "cash"
}

# Get order details
GET /api/orders/{id}
Authorization: Bearer TOKEN

# Cancel order
POST /api/orders/{id}/cancel
Authorization: Bearer TOKEN
```

**Addresses**
```bash
# List my addresses
GET /api/addresses
Authorization: Bearer TOKEN

# Create address
POST /api/addresses
Authorization: Bearer TOKEN
{
  "line1": "123 Main St",
  "city": "London",
  "postcode": "SW1A 1AA",
  "country": "United Kingdom"
}
```

### Admin Endpoints (role=admin)

**Shop Management** (super-admin only)
```bash
# List shops
GET /api/admin/shops
Authorization: Bearer TOKEN

# Create shop
POST /api/admin/shops
Authorization: Bearer TOKEN

# Get current shop
GET /api/admin/shops/current
Authorization: Bearer TOKEN

# Update current shop
PATCH /api/admin/shops/current
Authorization: Bearer TOKEN
```

**Products** (shop-admin only)
```bash
# List shop products
GET /api/admin/products
Authorization: Bearer TOKEN

# Create product
POST /api/admin/products
Authorization: Bearer TOKEN

# Update product
PATCH /api/admin/products/{id}
Authorization: Bearer TOKEN

# Delete product
DELETE /api/admin/products/{id}
Authorization: Bearer TOKEN
```

**Orders** (shop-admin only)
```bash
# List shop orders
GET /api/admin/orders
Authorization: Bearer TOKEN

# Get order details
GET /api/admin/orders/{id}
Authorization: Bearer TOKEN

# Update order status
PATCH /api/admin/orders/{id}/status
Authorization: Bearer TOKEN
{ "status": "confirmed" }

# Update payment status
PATCH /api/admin/orders/{id}/payment-status
Authorization: Bearer TOKEN
{ "payment_status": "paid" }
```

## Troubleshooting

### "Shop not found" error
- Check query parameter: `?shop=abc-grocery`
- Verify shop exists in database: `php artisan tinker; App\Models\Shop::all();`
- Check shop is_active = true

### Can't login
- Verify phone number format: `+44...` (with country code)
- Check OTP is `123456` in development
- Verify user exists for that phone in current shop

### Product images not uploading
- Configure AWS credentials in `.env` (optional for development)
- Or use local storage: File will be stored locally

### Orders not appearing
- Check you're in the correct shop
- Admin only sees orders for their shop
- Verify order is_active = true

### API returns wrong shop data
- Verify `?shop=slug` parameter is correct
- Check in browser DevTools → Application → Cookies for shop context
- Clear cache: `php artisan cache:clear`

## Next Steps

1. **Read Documentation**
   - [MULTI_TENANCY.md](MULTI_TENANCY.md) - Architecture details
   - [README.md](README.md) - Full feature list
   - [.github/copilot-instructions.md](.github/copilot-instructions.md) - Development guide

2. **Explore Codebase**
   - `app/Models/Shop.php` - Shop model
   - `app/Services/ShopContext.php` - Shop detection service
   - `app/Http/Controllers/Admin/ShopController.php` - Shop management

3. **Development**
   - Start modifying products and orders
   - Add your own features
   - Test with multiple shops

4. **Deployment**
   - See [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md)
   - Set up production domains
   - Configure AWS services
   - Monitor in production

## Support

- **Issues**: Check GitHub issues
- **Documentation**: See docs files in root
- **Questions**: Review code comments

## Key Commands

```bash
# Migrations
php artisan migrate                # Run migrations
php artisan migrate:rollback       # Undo last batch
php artisan make:migration name    # Create new migration

# Database
php artisan db:seed               # Seed demo data
php artisan tinker                # Interactive shell

# Cache
php artisan cache:clear           # Clear all cache
php artisan config:clear          # Clear config cache

# Development
php artisan serve                 # Start server (port 8000)
npm run dev                        # Build assets on change
npm run build                      # Production build

# Testing
php artisan test                  # Run tests
php artisan test --filter=test    # Run specific test
```

---

**Ready to build?** Start with creating a product and placing an order!
