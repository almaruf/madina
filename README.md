# ABC Grocery Shop - Multi-Tenant E-Commerce Platform

> **A Production-Ready Laravel 12 Multi-Tenant Grocery E-commerce Platform**

Comprehensive e-commerce solution enabling unlimited grocery shops from a single deployment with complete database-driven data isolation, phone-based authentication, and AWS integration.

---

## 📖 Table of Contents

1. [Overview](#overview)
2. [Features](#features)
3. [Technology Stack](#technology-stack)
4. [Multi-Tenancy Architecture](#multi-tenancy-architecture)
5. [Quick Start](#quick-start)
6. [Installation](#installation)
7. [Configuration](#configuration)
8. [User Roles & Access Control](#user-roles--access-control)
9. [API Documentation](#api-documentation)
10. [Database Schema](#database-schema)
11. [Development Guidelines](#development-guidelines)
12. [Deployment](#deployment)
13. [Troubleshooting](#troubleshooting)
14. [Documentation Index](#documentation-index)

---

## Overview

ABC Grocery Shop is a **production-ready multi-tenant e-commerce platform** designed for grocery businesses. Deploy once, serve unlimited shops with complete data isolation.

## Multi-Shop Architecture

This application is designed to support multiple grocery shops from a single codebase and database. Each shop's data is completely isolated using `shop_id` foreign keys. Shops are automatically detected by their domain or URL parameter.

### How It Works
1. **Single Deployment**: Deploy once, serve unlimited shops
2. **Domain-Based Shop Detection**: Each shop has its own domain (e.g., shop1.example.com, shop2.example.com)
3. **Query Parameter Fallback**: In development, use `?shop=slug` to test different shops
4. **Complete Data Isolation**: Each shop's orders, products, users, and categories are separate
5. **Shared Infrastructure**: AWS S3, SES, Twilio credentials can be shared or shop-specific

## Features

### Customer Features
- **Phone-based Authentication**: Register and login using phone number with OTP verification
- **Product Browsing**: Browse products by category, type, and search
- **Flexible Product Variations**: Products with multiple sizes, weights, and prices
- **Shopping Cart & Checkout**: Complete order placement with customizable options
- **Delivery & Collection**: Choose preferred time slots for delivery or collection
- **Address Management**: Save multiple delivery addresses
- **Order Tracking**: View order history and current order status
- **Multi-Shop Support**: All shops accessible from single deployment

### Admin Features
- **Shop Management**: Create and manage multiple shops (admin only)
- **Product Management**: Create, update, and delete products with variations (shop-specific)
- **Image Management**: Upload product images to AWS S3
- **Order Management**: View, update order status and payment status
- **Delivery Slot Management**: Create and manage delivery/collection time slots
- **Category Management**: Organize products into hierarchical categories
- **Dashboard**: View statistics and recent orders (shop-specific)
- **Role-Based Access**: Three-tier role system (Super Admin, Admin, Customer)

### Technical Features
- **Database-Driven Multi-Tenancy**: Each shop's data isolated by shop_id
- **Automatic Shop Detection**: Middleware detects shop by domain or query parameter
- **Role-Based Access Control**: Super Admin, Shop Admin, and Customer roles
- **Admin User Management**: Super admin creates and manages shop admins
- **Phone-Based OTP Auth**: Secure authentication via SMS
- **SQLite Database**: Lightweight database for development and small deployments
- **AWS S3 Integration**: Store product images on AWS S3
- **AWS SES**: Send emails via Amazon SES
- **Twilio Integration**: SMS-based OTP verification
- **RESTful API**: Complete API for mobile and web applications
- **Sanctum Authentication**: Secure API authentication

## Technology Stack

- **Framework**: Laravel 12
- **Database**: SQLite (can be changed to MySQL/PostgreSQL)
- **File Storage**: AWS S3
- **Email**: AWS SES
- **SMS**: Twilio
- **Authentication**: Laravel Sanctum
- **Frontend**: Blade templates with Tailwind CSS

## Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- SQLite (or MySQL/PostgreSQL if preferred)
- AWS Account (for S3 and SES)
- Twilio Account (for SMS OTP)

## Shop Configuration

Database-driven multi-tenancy: Each shop is stored in the database with complete data isolation. See the [.github/copilot-instructions.md](.github/copilot-instructions.md#multi-shop-database-architecture) for technical details.

### Quick Multi-Tenant Setup
1. Run migrations: `php artisan migrate`
2. Seed initial shop: `php artisan db:seed`
3. Access via query param: `http://localhost:8000/?shop=abc-grocery`

## Installation & Setup

### 1. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 2. Environment Configuration

```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Configure Environment Variables

Edit the `.env` file and configure:

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

# Default Delivery Configuration (can be overridden per shop)
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

# Seed database with initial shop and sample data
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

## Running the Application

### Development Server

```bash
# Start Laravel development server
php artisan serve

# In another terminal, start Vite for asset compilation
npm run dev
```

The application will be available at `http://localhost:8000`

Access the demo shop with query parameter: `http://localhost:8000/?shop=abc-grocery`

### Demo Credentials

**Super Admin** (Can create shops and manage admins)
- Phone: `4407849261469`
- Email: `maruf.sylhet@gmail.com`
- Name: Al Maruf
- OTP: `123456` (development only)

**Shop Admin** (Can manage ABC Grocery Shop)
- Phone: `+441234567890`
- Email: `admin@example.com`
- Name: Admin User
- OTP: `123456` (development only)

**Customer** (Can browse products and place orders)
- Phone: `+441234567891`
- Email: `customer@test.com`
- Name: Test Customer
- OTP: `123456` (development only)

### Production Deployment

```bash
# Build production assets
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
chmod -R 775 storage bootstrap/cache
```

## API Endpoints

### Authentication
- `POST /api/auth/send-otp` - Send OTP to phone number
- `POST /api/auth/verify-otp` - Verify OTP and login/register
- `POST /api/auth/logout` - Logout (authenticated)
- `GET /api/auth/user` - Get current user (authenticated)

### Products
- `GET /api/products` - List all products (current shop only)
- `GET /api/products/{slug}` - Get product details
- `GET /api/categories` - List all categories (current shop only)

### Orders (Authenticated)
- `GET /api/orders` - List user's orders (current shop only)
- `POST /api/orders` - Create new order
- `GET /api/orders/{id}` - Get order details
- `POST /api/orders/{id}/cancel` - Cancel order

### Admin Endpoints (Admin Only)
- `GET /api/admin/shops` - List all shops (super admin only)
- `POST /api/admin/shops` - Create new shop (super admin only)
- `GET /api/admin/shops/current` - View current shop details
- `PATCH /api/admin/shops/current` - Update current shop
- `GET /api/admin/products` - List shop products
- `POST /api/admin/products` - Create product
- `GET /api/admin/orders` - List shop orders
- `GET /api/admin/delivery-slots` - List shop delivery slots
- `GET /api/addresses` - List user's addresses
- `POST /api/addresses` - Create new address
- `GET /api/addresses/{id}` - Get address details
- `PUT /api/addresses/{id}` - Update address
- `DELETE /api/addresses/{id}` - Delete address

### Delivery Slots
- `GET /api/delivery-slots` - List available delivery/collection slots

### Admin Endpoints (Admin/Super Admin)
- `GET /api/admin/products` - List all products
- `POST /api/admin/products` - Create product
- `PUT /api/admin/products/{id}` - Update product
- `DELETE /api/admin/products/{id}` - Delete product
- `POST /api/admin/products/{id}/images` - Upload product image

- `GET /api/admin/orders` - List all orders
- `GET /api/admin/orders/stats` - Get order statistics
- `PATCH /api/admin/orders/{id}/status` - Update order status
- `PATCH /api/admin/orders/{id}/payment-status` - Update payment status

- `GET /api/admin/delivery-slots` - List delivery slots
- `POST /api/admin/delivery-slots` - Create delivery slot
- `POST /api/admin/delivery-slots/generate` - Generate slots for date range

- `GET /api/admin/categories` - List categories
- `POST /api/admin/categories` - Create category
- `PATCH /api/admin/categories/{id}` - Update category

### Super Admin Endpoints (Super Admin Only)
- `GET /api/admin/shops` - List all shops
- `POST /api/admin/shops` - Create new shop
- `GET /api/admin/shops/{id}` - Get shop details
- `PATCH /api/admin/shops/{id}` - Update shop
- `DELETE /api/admin/shops/{id}` - Delete shop

- `GET /api/admin/admin-users` - List admin users
- `POST /api/admin/admin-users` - Create admin user
- `GET /api/admin/admin-users/{id}` - Get admin details
- `PATCH /api/admin/admin-users/{id}` - Update admin user
- `DELETE /api/admin/admin-users/{id}` - Delete admin user
- `POST /api/admin/delivery-slots` - Create delivery slot
- `POST /api/admin/delivery-slots/generate` - Generate multiple slots

- `GET /api/admin/categories` - List categories
- `POST /api/admin/categories` - Create category
- `PUT /api/admin/categories/{id}` - Update category
- `DELETE /api/admin/categories/{id}` - Delete category

## Database Schema

### Main Tables
- **users** - Customer and admin accounts
- **otps** - OTP verification codes
- **addresses** - Customer delivery addresses
- **categories** - Product categories (hierarchical)
- **products** - Main product information
- **product_variations** - Product sizes, weights, prices
- **product_images** - Product images stored on S3
- **category_product** - Many-to-many relationship
- **delivery_slots** - Available delivery/collection times
- **orders** - Customer orders
- **order_items** - Individual items in orders

## Product Types

- **standard** - Regular grocery items
- **meat** - Halal meat products
- **frozen** - Frozen products
- **fresh** - Fresh produce
- **perishable** - Items with short shelf life

## Order Statuses

- **pending** - Order placed, awaiting confirmation
- **confirmed** - Order confirmed by admin
- **processing** - Order being prepared
- **ready** - Order ready for collection/delivery
- **out_for_delivery** - Order out for delivery
- **delivered** - Order delivered to customer
- **completed** - Order completed
- **cancelled** - Order cancelled
- **refunded** - Order refunded

## Development Notes

### 🔴 Common Issue: 401 Authentication Errors

If you experience 401 errors when making API calls from admin pages, see [AUTH_HEADERS_ISSUE.md](AUTH_HEADERS_ISSUE.md) for the complete solution. 

**TL;DR**: Every admin page must explicitly configure axios with the auth token at the start of its script section.

### Creating an Admin User

```php
php artisan tinker

$user = App\Models\User::create([
    'phone' => '+44 1234 567890',
    'name' => 'Admin User',
    'email' => 'admin@madinahalalshop.com',
    'role' => 'admin',
    'phone_verified' => true,
    'phone_verified_at' => now(),
]);
```

### Testing OTP Without Twilio

During development, OTP codes are logged to `storage/logs/laravel.log` if Twilio credentials are not configured.

### AWS S3 Setup

1. Create an S3 bucket
2. Set up IAM user with S3 permissions
3. Configure CORS policy for image uploads
4. Update `.env` with credentials

### Switching to MySQL/PostgreSQL

Update `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=madina
DB_USERNAME=root
DB_PASSWORD=your_password
```

## Project Structure

```
madina/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/          # Public API controllers
│   │   │   └── Admin/        # Admin API controllers
│   │   └── Middleware/       # Custom middleware
│   ├── Models/               # Eloquent models
│   └── Services/             # Business logic services
├── database/
│   └── migrations/           # Database migrations
├── routes/
│   ├── api.php              # API routes
│   ├── web.php              # Web routes
│   └── console.php          # Console commands
├── resources/
│   ├── views/               # Blade templates
│   ├── css/                 # Stylesheets
│   └── js/                  # JavaScript
└── config/                  # Configuration files
```

## License

MIT

## Creating a New Shop Instance

To rebrand this application for a different shop:

1. **Edit Shop Configuration**
   ```bash
   nano config/shop.json
   ```
   Update shop name, location, contact details, features, etc.

2. **Update Environment**
   ```bash
   cp .env.example .env
   nano .env
   ```
   Update `APP_NAME`, `SHOP_PHONE`, `SHOP_EMAIL`

3. **Configure Services** (AWS, Twilio)
   Add your service credentials to `.env`

4. **Clear Cache**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

5. **Test**
   Visit your application and verify all shop details are correct

See [SHOP_CONFIGURATION.md](SHOP_CONFIGURATION.md) for complete documentation.

## Support

For support, check your shop configuration in `config/shop.json` for contact details.
