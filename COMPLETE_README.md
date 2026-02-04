# ABC Grocery Shop - Multi-Tenant E-Commerce Platform

> **A Production-Ready Laravel 12 Multi-Tenant Grocery E-commerce Platform**

Comprehensive e-commerce solution enabling unlimited grocery shops from a single deployment with complete database-driven data isolation, phone-based authentication, and AWS integration.

---

## 📖 Table of Contents

- [Overview](#overview)
- [Key Features](#key-features)
- [Technology Stack](#technology-stack)
- [Multi-Tenancy Architecture](#multi-tenancy-architecture)
- [Quick Start (5 Minutes)](#quick-start-5-minutes)
- [Full Installation](#full-installation)
- [User Roles & Access Control](#user-roles--access-control)
- [API Documentation](#api-documentation)
- [Database Schema](#database-schema)
- [Configuration Guide](#configuration-guide)
- [Development Guidelines](#development-guidelines)
- [Deployment](#deployment)
- [Project Structure](#project-structure)
- [Troubleshooting](#troubleshooting)
- [Additional Documentation](#additional-documentation)

---

## Overview

ABC Grocery Shop is a **production-ready multi-tenant e-commerce platform** built with Laravel 12. It enables you to:

- ✅ **Deploy once, serve unlimited shops** from a single codebase
- ✅ **Complete data isolation** - Each shop's data is completely separate
- ✅ **Automatic shop detection** - By domain (production) or URL parameter (development)
- ✅ **Phone-based authentication** - OTP verification via SMS (Twilio)
- ✅ **Full product management** - Products with multiple variations, sizes, and pricing
- ✅ **Order processing** - Complete order management with delivery slots
- ✅ **AWS integration** - S3 for images, SES for emails
- ✅ **Role-based access control** - 5-level user hierarchy (super_admin → staff)
- ✅ **RESTful API** - Complete API for mobile/web apps

### Multi-Shop Architecture

This application is designed to support multiple grocery shops from a single codebase and database:

1. **Single Deployment** → Deploy once, serve unlimited shops
2. **Domain-Based Detection** → Each shop has its own domain (e.g., shop1.com, shop2.com)
3. **Query Parameter Fallback** → Use `?shop=slug` in development
4. **Complete Data Isolation** → Each shop's orders, products, users separate
5. **Shared Infrastructure** → AWS S3, SES, Twilio shared or shop-specific

---

## Key Features

### Customer Features
- **Phone-Based Authentication** - Register and login using phone + OTP
- **Product Browsing** - Browse by category, search, filter
- **Product Variations** - Multiple sizes, weights, prices per product
- **Shopping Cart & Checkout** - Complete order placement
- **Delivery & Collection** - Choose delivery or pickup with time slots
- **Address Management** - Save multiple delivery addresses
- **Order Tracking** - View order history and current status
- **Halal Products** - Special handling for halal meat products

### Admin Features (Per Shop)
- **Product Management** - Create, update, delete products with variations
- **Image Management** - Upload multiple product images to AWS S3
- **Order Management** - View, update order status and payment status
- **Delivery Slots** - Create and manage delivery/collection time slots
- **Category Management** - Organize products into hierarchical categories
- **Offers & Promotions** - 7 offer types (BOGO, discounts, bundles, etc.)
- **Dashboard** - Statistics and recent orders
- **User Management** - Manage shop staff and customers

### Super Admin Features (Platform-Wide)
- **Shop Management** - Create and manage unlimited shops
- **Admin User Management** - Create shop admins and assign permissions
- **Cross-Shop Analytics** - View all shops and their performance
- **Platform Configuration** - Manage global settings

### Technical Features
- **Database-Driven Multi-Tenancy** - Complete isolation via shop_id
- **Automatic Shop Detection** - Middleware detects shop from domain/parameter
- **Role-Based Access Control** - 5 levels: super_admin, admin, owner, staff, customer
- **Phone-Based OTP Auth** - Secure SMS verification
- **SQLite/MySQL/PostgreSQL** - Flexible database options
- **AWS S3 Integration** - Cloud storage for images
- **AWS SES** - Email delivery service
- **Twilio Integration** - SMS OTP verification
- **RESTful API** - Complete API for all operations
- **Sanctum Authentication** - Secure token-based API auth

---

## Technology Stack

| Component | Technology |
|-----------|-----------|
| Framework | Laravel 12 (PHP 8.2+) |
| Database | SQLite (default), MySQL, PostgreSQL |
| File Storage | AWS S3 |
| Email | AWS SES |
| SMS | Twilio |
| Authentication | Laravel Sanctum + OTP |
| Frontend | Blade Templates + Tailwind CSS |
| Build Tool | Vite |
| API | RESTful JSON API |

---

## Multi-Tenancy Architecture

### Shop Detection Flow

```
HTTP Request
    ↓
DetectShop Middleware
    ├→ Try: Domain/Host header (production)
    ├→ Fallback: ?shop=slug query param (development)
    └→ Default: First active shop
    ↓
ShopContext Service
    ├→ Store current shop in request context
    ├→ Cache for 1 hour
    └→ Make available to controllers/services
    ↓
All Queries Filtered by shop_id
All Data Creation Assigned shop_id
```

### Database Schema

**Master Table:**
- `shops` - Shop configuration (name, domain, settings, branding)

**Tenant Tables** (All have `shop_id` foreign key):
- `users` - Shop customers, staff, admins
- `products` - Product catalog
- `product_variations` - Pricing and stock
- `categories` - Product categories
- `orders` - Customer orders
- `order_items` - Order line items
- `addresses` - Delivery addresses
- `delivery_slots` - Delivery time slots
- `otps` - Phone verification codes

### Key Services

**ShopContext** (`app/Services/ShopContext.php`)
```php
$shopId = ShopContext::getShopId();
$shop = ShopContext::getShop();
```

**ShopConfigService** (`app/Services/ShopConfigService.php`)
```php
$config = app(ShopConfigService::class);
$name = $config->name();
$phone = $config->phone();
```

---

## Quick Start (5 Minutes)

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & npm
- SQLite3

### Installation
```bash
# 1. Install dependencies
composer install && npm install

# 2. Environment setup
cp .env.example .env
php artisan key:generate

# 3. Create database
touch database/database.sqlite

# 4. Run migrations and seed data
php artisan migrate
php artisan db:seed

# 5. Start servers
php artisan serve        # Terminal 1 - http://localhost:8000
npm run dev             # Terminal 2 - Asset compilation
```

### First Access
```bash
# Access demo shop
http://localhost:8000/?shop=abc-grocery

# Demo credentials (OTP: 123456 in development)
Super Admin: 4407849261469 (maruf.sylhet@gmail.com)
Shop Admin:  +441234567890 (admin@example.com)
Customer:    +441234567891 (customer@test.com)
```

---

## Full Installation

### 1. Install Dependencies

```bash
# PHP dependencies
composer install

# Node dependencies
npm install
```

### 2. Environment Configuration

```bash
# Copy example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Configure Environment Variables

Edit `.env` file:

```env
# Application
APP_NAME="ABC Grocery Shop"
APP_URL=http://localhost:8000

# Database (SQLite by default)
DB_CONNECTION=sqlite

# AWS Configuration
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=eu-west-2
AWS_BUCKET=your_bucket_name

# Twilio for OTP
TWILIO_SID=your_twilio_sid
TWILIO_TOKEN=your_twilio_token
TWILIO_FROM=your_twilio_phone_number

# Default Delivery Configuration
MIN_ORDER_AMOUNT=20.00
DELIVERY_FEE=3.99
FREE_DELIVERY_THRESHOLD=50.00
```

### 4. Database Setup

```bash
# Create SQLite database file
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed database with demo data
php artisan db:seed
```

### 5. Storage Setup

```bash
# Create symbolic link for public storage
php artisan storage:link
```

### 6. Build Frontend Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 7. Run Application

```bash
# Development server
php artisan serve

# Or use included scripts
./start.sh    # Start server in background
./stop.sh     # Stop background server
```

---

## User Roles & Access Control

### Role Hierarchy (5 Levels)

#### 1. Super Admin (Level 5)
**Example:** Al Maruf (maruf.sylhet@gmail.com)

**Capabilities:**
- ✅ Create/manage shops
- ✅ Create/manage admin users
- ✅ Access all shops' data
- ✅ Platform-wide analytics
- ❌ Not tied to any specific shop (`shop_id = NULL`)

**Access:**
```
POST   /api/admin/shops              # Create shop
GET    /api/admin/shops              # List all shops
PATCH  /api/admin/shops/{id}         # Update shop
DELETE /api/admin/shops/{id}         # Delete shop
POST   /api/admin/admin-users        # Create admin
GET    /api/admin/admin-users        # List admins
+ All admin features
```

#### 2. Admin (Level 4)
**Example:** Shop Manager

**Capabilities:**
- ✅ Manage products and inventory
- ✅ Process orders
- ✅ Manage delivery slots
- ✅ Manage categories
- ✅ View shop analytics
- ❌ Cannot create shops
- ❌ Cannot create admin users
- ❌ Tied to specific shop (`shop_id` set)

#### 3. Owner (Level 3)
**Shop owner with full shop access**

#### 4. Staff (Level 2)
**Shop staff with limited access**

#### 5. Customer (Level 1)
**Public users who can browse and order**

**Capabilities:**
- ✅ Browse products
- ✅ Place orders
- ✅ Track orders
- ✅ Manage addresses
- ❌ No admin access

### Authentication Flow

```bash
# 1. Send OTP
POST /api/auth/send-otp
{"phone": "+441234567890"}

# 2. Verify OTP
POST /api/auth/verify-otp
{"phone": "+441234567890", "otp": "123456"}

# Response includes:
{
    "user": {...},
    "token": "sanctuary_token_...",
    "token_expires_in": 3600
}

# 3. Use token in subsequent requests
Authorization: Bearer sanctuary_token_...
```

---

## API Documentation

### Authentication Endpoints

```
POST   /api/auth/send-otp           # Send OTP to phone
POST   /api/auth/verify-otp         # Verify OTP and login
POST   /api/auth/logout             # Logout
GET    /api/auth/user               # Get current user
```

### Public Endpoints (No Auth Required)

```
GET    /api/products                # List all products
GET    /api/products/{slug}         # Get product details
GET    /api/categories              # List all categories
GET    /api/delivery-slots          # List available slots
GET    /api/offers/active           # Get active offers
GET    /api/shop/config             # Get shop configuration
```

### Customer Endpoints (Auth Required)

```
# Orders
GET    /api/orders                  # List my orders
POST   /api/orders                  # Create order
GET    /api/orders/{id}             # Get order details
POST   /api/orders/{id}/cancel      # Cancel order

# Addresses
GET    /api/addresses               # List my addresses
POST   /api/addresses               # Create address
GET    /api/addresses/{id}          # Get address details
PUT    /api/addresses/{id}          # Update address
DELETE /api/addresses/{id}          # Delete address
```

### Admin Endpoints (Admin Role Required)

```
# Products
GET    /api/admin/products          # List shop products
POST   /api/admin/products          # Create product
GET    /api/admin/products/{slug}   # Get product
PUT    /api/admin/products/{slug}   # Update product
DELETE /api/admin/products/{slug}   # Delete (archive) product
POST   /api/admin/products/{slug}/restore       # Restore product
DELETE /api/admin/products/{slug}/force         # Permanent delete
POST   /api/admin/products/{slug}/images        # Upload images

# Orders
GET    /api/admin/orders            # List shop orders
GET    /api/admin/orders/{id}       # Get order details
PATCH  /api/admin/orders/{id}/status            # Update order status
PATCH  /api/admin/orders/{id}/payment-status    # Update payment

# Delivery Slots
GET    /api/admin/delivery-slots    # List slots
POST   /api/admin/delivery-slots    # Create slot
POST   /api/admin/delivery-slots/generate       # Bulk create

# Categories
GET    /api/admin/categories         # List categories
POST   /api/admin/categories         # Create category
PATCH  /api/admin/categories/{slug}  # Update category
DELETE /api/admin/categories/{slug}  # Delete category

# Offers
GET    /api/admin/offers             # List offers
POST   /api/admin/offers             # Create offer
PUT    /api/admin/offers/{id}        # Update offer
DELETE /api/admin/offers/{id}        # Delete offer
POST   /api/admin/offers/{id}/toggle-status     # Toggle active
GET    /api/admin/offers/{id}/products          # Get offer products
POST   /api/admin/offers/{id}/products          # Add product to offer
DELETE /api/admin/offers/{id}/products/{productId}  # Remove product

# Shop Management (Current Shop)
GET    /api/admin/shops/current      # Get current shop
PATCH  /api/admin/shops/current      # Update current shop
```

### Super Admin Endpoints (Super Admin Only)

```
# Shop Management
GET    /api/admin/shops              # List all shops
POST   /api/admin/shops              # Create shop
GET    /api/admin/shops/{slug}       # Get shop details
PATCH  /api/admin/shops/{slug}       # Update shop
DELETE /api/admin/shops/{slug}       # Delete shop

# Admin Users
POST   /api/admin/admin-users        # Create admin user
GET    /api/admin/admin-users        # List admin users
GET    /api/admin/admin-users/{id}   # Get admin details
PATCH  /api/admin/admin-users/{id}   # Update admin
DELETE /api/admin/admin-users/{id}   # Delete admin
```

---

## Database Schema

### Core Tables

#### shops
Master tenant configuration table:
```sql
CREATE TABLE shops (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    domain VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    tagline VARCHAR(255),
    address_line_1 VARCHAR(255),
    city VARCHAR(255),
    postcode VARCHAR(20),
    country VARCHAR(255),
    phone VARCHAR(20),
    email VARCHAR(255),
    currency VARCHAR(3) DEFAULT 'GBP',
    currency_symbol VARCHAR(10) DEFAULT '£',
    is_active BOOLEAN DEFAULT true,
    settings JSON,
    branding JSON,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### users
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY,
    shop_id BIGINT NULL,                           -- NULL for super_admin/admin
    phone VARCHAR(20) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE,
    name VARCHAR(255),
    role ENUM('super_admin','admin','owner','staff','customer') DEFAULT 'customer',
    phone_verified BOOLEAN DEFAULT false,
    phone_verified_at TIMESTAMP,
    deleted_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
);
```

#### products
```sql
CREATE TABLE products (
    id BIGINT PRIMARY KEY,
    shop_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    type ENUM('standard','meat','frozen','fresh','perishable'),
    is_halal BOOLEAN DEFAULT false,
    meat_type VARCHAR(50),
    cut_type VARCHAR(50),
    is_active BOOLEAN DEFAULT true,
    is_featured BOOLEAN DEFAULT false,
    times_purchased INT DEFAULT 0,
    deleted_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
);
```

#### product_variations
```sql
CREATE TABLE product_variations (
    id BIGINT PRIMARY KEY,
    product_id BIGINT NOT NULL,
    size VARCHAR(50),
    weight VARCHAR(50),
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT DEFAULT 0,
    is_default BOOLEAN DEFAULT false,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
```

#### orders
```sql
CREATE TABLE orders (
    id BIGINT PRIMARY KEY,
    shop_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    delivery_slot_id BIGINT,
    address_id BIGINT,
    subtotal DECIMAL(10,2) NOT NULL,
    delivery_fee DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending','confirmed','processing','ready','out_for_delivery','delivered','completed','cancelled','refunded'),
    payment_status ENUM('pending','paid','failed','refunded'),
    payment_method ENUM('cash','card','online'),
    fulfillment_type ENUM('delivery','collection'),
    notes TEXT,
    confirmed_at TIMESTAMP,
    deleted_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### Additional Tables

- **categories** - Hierarchical product categories (with parent_id for subcategories)
- **product_images** - Multiple images per product (stored on S3)
- **category_product** - Many-to-many pivot (products can be in multiple categories)
- **order_items** - Individual items in orders
- **addresses** - Customer delivery addresses
- **delivery_slots** - Available delivery/collection time slots
- **offers** - Promotional offers (7 types supported)
- **offer_product** - Products included in offers
- **shop_banners** - Homepage banner images
- **otps** - Phone verification codes

---

## Configuration Guide

### Shop Configuration

Each shop's configuration is stored in the database. Key settings include:

**Basic Information:**
- Name, slug, domain
- Description, tagline
- Address, city, postcode, country
- Phone, email, support email

**Delivery & Pricing:**
- Currency, currency symbol
- Delivery fee, min order amount
- Free delivery threshold
- Delivery radius

**Features:**
- Delivery enabled/disabled
- Collection enabled/disabled
- Online payment enabled/disabled
- Halal products
- Organic products

**Legal & VAT:**
- Company registration number
- VAT registration
- VAT number
- VAT rate
- Prices include VAT flag

**Branding:**
- Primary/secondary colors
- Logo URL
- Favicon URL

**Social Media:**
- Facebook, Instagram, Twitter URLs

**Operating Hours:**
- Hours for each day of the week

### Accessing Shop Configuration in Code

**In Controllers:**
```php
use App\Services\ShopConfigService;

$shopConfig = app(ShopConfigService::class);
$name = $shopConfig->name();
$phone = $shopConfig->phone();
$currency = $shopConfig->currency();
```

**In Views:**
```blade
{{ app(\App\Services\ShopConfigService::class)->name() }}
{{ app(\App\Services\ShopConfigService::class)->phone() }}
```

**Getting Current Shop:**
```php
use App\Services\ShopContext;

$shopId = ShopContext::getShopId();
$shop = ShopContext::getShop();
```

### Creating a New Shop

**Via API (requires super admin token):**
```bash
curl -X POST http://localhost:8000/api/admin/shops \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Fresh Market",
    "slug": "fresh-market",
    "domain": "freshmarket.example.com",
    "description": "Fresh produce delivered daily",
    "address_line_1": "123 Market St",
    "city": "London",
    "postcode": "SW1A 1AA",
    "country": "United Kingdom",
    "phone": "+442071234567",
    "email": "contact@freshmarket.com",
    "currency": "GBP",
    "currency_symbol": "£",
    "is_active": true
  }'
```

**Via Database:**
```bash
php artisan tinker

>>> App\Models\Shop::create([
    'name' => 'Fresh Market',
    'slug' => 'fresh-market',
    'domain' => 'freshmarket.example.com',
    'description' => 'Fresh produce',
    'city' => 'London',
    'postcode' => 'SW1A 1AA',
    'country' => 'United Kingdom',
    'phone' => '+442071234567',
    'email' => 'contact@freshmarket.com',
    'currency' => 'GBP',
    'currency_symbol' => '£',
    'is_active' => true,
]);
```

---

## Development Guidelines

### 🔴 Critical: Authentication Headers Issue

**IMPORTANT:** Every admin page that makes API calls MUST configure axios with the auth token at the start of its script section. See [AUTH_HEADERS_ISSUE.md](docs/archive/AUTH_HEADERS_ISSUE.md) for complete details.

**Required at the top of every admin page script:**
```javascript
<script>
// CRITICAL: Set auth token for axios
const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
if (!token) {
    window.location.href = '/admin/login';
} else {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    axios.defaults.headers.common['Accept'] = 'application/json';
    axios.defaults.headers.common['Content-Type'] = 'application/json';
}

// Your page code here...
</script>
```

### Multi-Tenancy Rules (Always Follow)

1. **Never hardcode shop data**
   ```php
   // ❌ WRONG
   return "Welcome to ABC Grocery";
   
   // ✅ CORRECT
   return "Welcome to " . app(ShopConfigService::class)->name();
   ```

2. **Always filter by shop_id**
   ```php
   // ❌ WRONG
   $products = Product::all();
   
   // ✅ CORRECT
   $shopId = ShopContext::getShopId();
   $products = Product::where('shop_id', $shopId)->get();
   ```

3. **Always assign shop_id on creation**
   ```php
   // ❌ WRONG
   $product = Product::create(['name' => 'Apple']);
   
   // ✅ CORRECT
   $product = Product::create([
       'name' => 'Apple',
       'shop_id' => ShopContext::getShopId(),
   ]);
   ```

4. **Use relationships**
   ```php
   // ❌ WRONG
   $users = DB::table('users')->get();
   
   // ✅ CORRECT
   $shop = ShopContext::getShop();
   $users = $shop->users()->get();
   ```

### Code Standards

**Naming Conventions:**
- Controllers: `PascalCase` + `Controller` suffix
- Models: `PascalCase` (singular)
- Database tables: `snake_case` (plural)
- Routes: `kebab-case`
- Variables: `camelCase`
- Constants: `UPPER_SNAKE_CASE`

**Validation Pattern:**
```php
$validator = Validator::make($request->all(), [
    'field' => 'required|string|max:255',
]);

if ($validator->fails()) {
    return response()->json(['errors' => $validator->errors()], 422);
}
```

**Database Transactions:**
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

---

## Deployment

### Production Checklist

See [DEPLOYMENT_CHECKLIST.md](docs/archive/DEPLOYMENT_CHECKLIST.md) for complete deployment guide.

**Quick Deployment Steps:**

1. **Prepare Server**
```bash
composer install --no-dev --optimize-autoloader
npm install --production
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

2. **Database**
```bash
php artisan migrate --force
```

3. **Permissions**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

4. **Domain Configuration**
Set up DNS records for each shop domain:
```
shop1.example.com → your.server.ip
shop2.example.com → your.server.ip
```

5. **SSL Certificates**
Install SSL certificates (wildcard recommended):
```bash
certbot --nginx -d *.example.com
```

6. **Environment**
Update production `.env`:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourshop.com

# Production database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Production AWS credentials
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...

# Production Twilio
TWILIO_SID=...
TWILIO_TOKEN=...
```

### Web Server Configuration

**Nginx:**
```nginx
server {
    listen 443 ssl http2;
    server_name *.example.com;
    
    root /var/www/app/public;
    index index.php;
    
    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

---

## Project Structure

```
madina/
├── app/
│   ├── Console/Commands/
│   ├── Helpers/helpers.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/              # Public API controllers
│   │   │   ├── Admin/            # Admin API controllers
│   │   │   └── Shop/             # Shop frontend controllers
│   │   └── Middleware/
│   │       ├── DetectShop.php    # Shop detection middleware
│   │       ├── AdminMiddleware.php
│   │       └── SuperAdminMiddleware.php
│   ├── Jobs/
│   │   └── SendOrderConfirmationEmail.php
│   ├── Mail/
│   │   └── OrderConfirmed.php
│   ├── Models/
│   │   ├── Shop.php              # Shop model
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Order.php
│   │   └── ...
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   └── Services/
│       ├── ShopContext.php       # Shop context service
│       ├── ShopConfigService.php # Shop configuration
│       └── OtpService.php        # OTP handling
├── bootstrap/
│   └── app.php                   # App bootstrap
├── config/
│   ├── auth.php
│   ├── database.php
│   ├── filesystems.php
│   ├── mail.php
│   └── services.php
├── database/
│   ├── migrations/
│   │   ├── create_shops_table.php
│   │   ├── add_shop_id_to_tables.php
│   │   └── ...
│   ├── seeders/
│   │   └── DatabaseSeeder.php
│   └── database.sqlite
├── public/
│   ├── index.php
│   └── build/                    # Compiled assets
├── resources/
│   ├── css/
│   ├── js/
│   │   └── admin/                # Admin panel JavaScript modules
│   └── views/
│       ├── admin/                # Admin panel views
│       └── shop/                 # Shop frontend views
├── routes/
│   ├── api.php                   # API routes
│   ├── web.php                   # Web routes
│   └── console.php
├── storage/
│   ├── app/
│   ├── framework/
│   └── logs/
├── .env                          # Environment configuration
├── .env.example                  # Environment template
├── composer.json                 # PHP dependencies
├── package.json                  # Node dependencies
├── vite.config.js                # Vite build configuration
├── start.sh                      # Start server script
└── stop.sh                       # Stop server script
```

---

## Troubleshooting

### Common Issues

#### 1. Shop Not Found Error
**Symptoms:** "Shop not found" error when accessing application

**Solutions:**
- Verify shop exists: `php artisan tinker; Shop::all();`
- Check shop is active: `Shop::where('slug', 'abc-grocery')->first()->is_active`
- In dev, ensure correct query param: `?shop=abc-grocery`
- Clear cache: `php artisan cache:clear`

#### 2. 401 Authentication Errors
**Symptoms:** API calls returning 401 Unauthorized

**Solutions:**
- Check auth token in localStorage (DevTools → Application → Local Storage)
- Verify Authorization header in request (DevTools → Network → Headers)
- See [AUTH_HEADERS_ISSUE.md](docs/archive/AUTH_HEADERS_ISSUE.md) for complete guide
- Ensure axios is configured with token at top of script

#### 3. Can't Login
**Symptoms:** OTP verification fails

**Solutions:**
- Verify phone format: Must include country code `+44...`
- Check OTP: In dev mode, OTP is `123456`
- Verify user exists: `User::where('phone', '+441234567890')->first()`
- Check Twilio credentials if in production

#### 4. Product Images Not Uploading
**Symptoms:** Image upload fails or returns error

**Solutions:**
- Configure AWS credentials in `.env`
- Check S3 bucket permissions
- For dev: File will store locally if AWS not configured
- Verify `php artisan storage:link` was run

#### 5. Orders Not Appearing
**Symptoms:** Orders list is empty in admin

**Solutions:**
- Check you're viewing correct shop: `?shop=correct-slug`
- Verify order has correct shop_id
- Check order is_active flag
- Clear cache: `php artisan cache:clear`

#### 6. VAT Not Showing
**Symptoms:** VAT line missing in cart/checkout

**Solutions:**
- Check shop `vat_registered` flag: `Shop::first()->vat_registered`
- Verify `prices_include_vat` setting
- Check shop config API: `GET /api/shop/config`
- If `prices_include_vat = true`, VAT line is hidden (VAT already in prices)

### Error Logging

**Laravel Logs:**
```bash
tail -f storage/logs/laravel.log
```

**Server Logs:**
```bash
# If using start.sh script
tail -f /tmp/laravel-server.log
```

### Cache Management

After configuration changes:
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
```

---

## Additional Documentation

This repository contains extensive documentation for all aspects of the platform:

### Quick Reference
- **[QUICKSTART.md](docs/archive/QUICKSTART.md)** - 5-minute setup guide
- **[BUILD.md](docs/archive/BUILD.md)** - Build instructions

### Architecture & Design
- **[MULTI_TENANCY.md](docs/archive/MULTI_TENANCY.md)** - Complete multi-tenancy architecture guide
- **[USER_ROLES.md](docs/archive/USER_ROLES.md)** - Role-based access control documentation
- **[SHOP_CONFIGURATION.md](docs/archive/SHOP_CONFIGURATION.md)** - Shop configuration guide

### Implementation Details
- **[OFFERS_IMPLEMENTATION.md](docs/archive/OFFERS_IMPLEMENTATION.md)** - Offers & promotions system
- **[EMAIL_SETUP.md](docs/archive/EMAIL_SETUP.md)** - Order confirmation emails
- **[SLUG_MIGRATION_COMPLETE.md](docs/archive/SLUG_MIGRATION_COMPLETE.md)** - Slug-based routing

### Operations & Maintenance
- **[DEPLOYMENT_CHECKLIST.md](docs/archive/DEPLOYMENT_CHECKLIST.md)** - Production deployment guide
- **[SERVER_MANAGEMENT.md](docs/archive/SERVER_MANAGEMENT.md)** - Server management scripts

### Troubleshooting & Issues
- **[AUTH_HEADERS_ISSUE.md](docs/archive/AUTH_HEADERS_ISSUE.md)** - 🔴 **CRITICAL** - Authentication troubleshooting
- **[BLADE_UPDATE_INSTRUCTIONS.md](docs/archive/BLADE_UPDATE_INSTRUCTIONS.md)** - Blade file updates

### Development Updates
- **[FEBRUARY_4_2026_UPDATES.md](docs/archive/FEBRUARY_4_2026_UPDATES.md)** - Latest session updates
- **[SEPARATE_FORM_SUBMISSIONS.md](docs/archive/SEPARATE_FORM_SUBMISSIONS.md)** - Shop edit form improvements
- **[ADMIN_SHOW_PAGES_EXTRACTION.md](docs/archive/ADMIN_SHOW_PAGES_EXTRACTION.md)** - JavaScript module extraction

### Status Reports
- **[IMPLEMENTATION_COMPLETE.md](docs/archive/IMPLEMENTATION_COMPLETE.md)** - Multi-tenancy completion report
- **[SUPER_ADMIN_READY.md](docs/archive/SUPER_ADMIN_READY.md)** - Super admin system ready
- **[SUPER_ADMIN_SETUP.md](docs/archive/SUPER_ADMIN_SETUP.md)** - Super admin setup guide
- **[VERIFICATION_CHECKLIST.md](docs/archive/VERIFICATION_CHECKLIST.md)** - Super admin verification
- **[PHASE1_COMPLETE.md](docs/archive/PHASE1_COMPLETE.md)** - Phase 1 progress report
- **[FINAL_STATUS.md](docs/archive/FINAL_STATUS.md)** - Major milestone achievements
- **[SESSION_COMPLETE.md](docs/archive/SESSION_COMPLETE.md)** - Session summary
- **[IMPLEMENTATION_STATUS.md](docs/archive/IMPLEMENTATION_STATUS.md)** - Overall implementation status
- **[IMPLEMENTATION_SUMMARY.md](docs/archive/IMPLEMENTATION_SUMMARY.md)** - Features and patterns
- **[CHANGES.md](docs/archive/CHANGES.md)** - Complete list of changes

---

## Support & Contributing

### Getting Help
- **Issues**: Check the documentation files above
- **Questions**: Review code comments in relevant files
- **Bugs**: Check GitHub issues or create new one

### Key Contacts
- **Super Admin**: Al Maruf (maruf.sylhet@gmail.com)
- **Technical Lead**: See repository owner

### Development Workflow
1. Always use feature branches
2. Follow Laravel conventions
3. Write migrations for schema changes
4. Update relevant documentation
5. Test multi-tenancy isolation
6. Clear caches after changes

---

## License

MIT License - See LICENSE file for details

---

## Credits

**Built with:**
- Laravel 12
- Tailwind CSS
- Alpine.js
- AWS Services
- Twilio

**Special Thanks:**
- Laravel Community
- Contributors
- Testers

---

**Last Updated:** February 4, 2026  
**Version:** 1.0  
**Status:** ✅ Production Ready

---

## Quick Commands Reference

```bash
# Development
php artisan serve               # Start server
npm run dev                     # Build assets (watch mode)
php artisan migrate             # Run migrations
php artisan db:seed             # Seed database

# Production
composer install --no-dev --optimize-autoloader
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Cache Management
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

# Database
php artisan migrate:rollback    # Undo last migration
php artisan tinker              # Interactive shell

# Server Management
./start.sh                      # Start background server
./stop.sh                       # Stop background server
tail -f /tmp/laravel-server.log # View server logs
```

---

**Ready to get started? See [QUICKSTART.md](docs/archive/QUICKSTART.md) for a 5-minute setup guide!** 🚀
