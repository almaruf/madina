# User Roles & Access Control

## Role Hierarchy

The platform has three user roles with different access levels:

### 1. Super Admin (maruf.sylhet@gmail.com)
**Purpose**: Platform management and shop creation

**Capabilities**:
- ✅ Create new shops
- ✅ Update shop details (name, address, currency, etc.)
- ✅ Delete shops
- ✅ View all shops
- ✅ Create admin users for any shop
- ✅ Update admin user details
- ✅ Delete admin users
- ✅ Access all admin features (products, orders, delivery slots, categories)

**Access Endpoints**:
```
POST   /api/admin/shops                 (Create shop)
GET    /api/admin/shops                 (List all shops)
PATCH  /api/admin/shops/{id}            (Update shop)
DELETE /api/admin/shops/{id}            (Delete shop)

POST   /api/admin/admin-users           (Create admin user)
GET    /api/admin/admin-users           (List admin users)
PATCH  /api/admin/admin-users/{id}      (Update admin user)
DELETE /api/admin/admin-users/{id}      (Delete admin user)

+ All admin features (products, orders, etc.)
```

**Login Credentials**:
- Phone: `4407849261469`
- Email: `maruf.sylhet@gmail.com`
- Name: Al Maruf
- OTP: `123456` (development only)

### 2. Admin (Shop-Specific)
**Purpose**: Manage products, orders, and operations for a specific shop

**Capabilities**:
- ✅ Create products with variations
- ✅ Update product details and pricing
- ✅ Delete products
- ✅ View shop orders
- ✅ Update order status (pending → confirmed → processing → ready → delivered)
- ✅ Update payment status
- ✅ Create delivery time slots
- ✅ Update delivery slots
- ✅ Create product categories
- ✅ Update category details
- ✅ View current shop details
- ✅ Update current shop details (limited)
- ❌ Cannot create shops
- ❌ Cannot manage other shops
- ❌ Cannot create admin users

**Access Endpoints**:
```
GET    /api/admin/products             (Own shop products)
POST   /api/admin/products             (Create product)
PATCH  /api/admin/products/{id}        (Update product)
DELETE /api/admin/products/{id}        (Delete product)

GET    /api/admin/orders               (Own shop orders)
GET    /api/admin/orders/{id}          (Order details)
PATCH  /api/admin/orders/{id}/status   (Update status)
PATCH  /api/admin/orders/{id}/payment-status

GET    /api/admin/delivery-slots       (Own shop slots)
POST   /api/admin/delivery-slots       (Create slot)
POST   /api/admin/delivery-slots/generate

GET    /api/admin/categories           (Own shop categories)
POST   /api/admin/categories           (Create category)
PATCH  /api/admin/categories/{id}      (Update category)

GET    /api/admin/shops/current        (View current shop)
PATCH  /api/admin/shops/current        (Update limited fields)
```

**Demo Login** (for ABC Grocery Shop):
- Phone: `+441234567890`
- Email: `admin@example.com`
- Name: Admin User
- OTP: `123456` (development only)

### 3. Customer (Public User)
**Purpose**: Browse products and place orders

**Capabilities**:
- ✅ Browse products
- ✅ View product details
- ✅ View categories
- ✅ View available delivery slots
- ✅ Create orders
- ✅ View own orders
- ✅ Cancel own orders
- ✅ Save delivery addresses
- ✅ Update own profile
- ❌ Cannot access admin features
- ❌ Cannot view other shops' orders

**Access Endpoints**:
```
GET    /api/products                   (List products)
GET    /api/products/{slug}            (Product details)
GET    /api/categories                 (List categories)
GET    /api/delivery-slots             (Available slots)

GET    /api/orders                     (Own orders)
POST   /api/orders                     (Create order)
GET    /api/orders/{id}                (Order details)
POST   /api/orders/{id}/cancel         (Cancel order)

GET    /api/addresses                  (Own addresses)
POST   /api/addresses                  (Create address)
PATCH  /api/addresses/{id}             (Update address)
DELETE /api/addresses/{id}             (Delete address)

GET    /api/auth/user                  (Profile)
```

**Demo Login** (for ABC Grocery Shop):
- Phone: `+441234567891`
- Email: `customer@test.com`
- Name: Test Customer
- OTP: `123456` (development only)

## Authentication Flow

### 1. OTP Login (Phone-Based)
```
POST /api/auth/send-otp
{
    "phone": "+441234567890"
}
↓
Receive OTP via SMS (or see in logs in dev)
↓
POST /api/auth/verify-otp
{
    "phone": "+441234567890",
    "otp": "123456"
}
↓
Response:
{
    "user": { "id": 1, "name": "...", "role": "admin", ... },
    "token": "sanctuary_token_...",
    "token_expires_in": 3600
}
```

### 2. Using Token
All subsequent requests use the token in header:
```
Authorization: Bearer sanctuary_token_...
```

## Creating New Admin Users

Only super admin can create admin users. Steps:

1. **Login as super admin** (maruf.sylhet@gmail.com)
2. **Create the shop** (if not exists)
   ```
   POST /api/admin/shops
   {
       "name": "New Shop",
       "slug": "new-shop",
       "domain": "newshop.example.com",
       ...
   }
   ```

3. **Create admin user for shop**
   ```
   POST /api/admin/admin-users
   {
       "phone": "+441234567892",
       "email": "shopadmin@example.com",
       "name": "Shop Admin",
       "shop_id": 2
   }
   ```

4. **Share credentials with shop admin**
   - Phone: +441234567892
   - They login and set their own password (future enhancement)
   - OTP: 123456 (development only)

## Middleware & Access Control

### Middleware Stack

**SuperAdminMiddleware** (app/Http/Middleware/SuperAdminMiddleware.php)
- Checks `user.role === 'super_admin'`
- Returns 403 Forbidden if not super admin
- Applied to: Shop creation, admin user management

**AdminMiddleware** (app/Http/Middleware/AdminMiddleware.php)
- Checks `user.role === 'admin' || user.role === 'super_admin'`
- Returns 403 Forbidden if neither
- Applied to: All /api/admin/* routes

**CustomerMiddleware** (app/Http/Middleware/CustomerMiddleware.php)
- Checks `user.role === 'customer'`
- Returns 403 Forbidden if not customer
- Applied to: Customer-only routes (future)

### Route Protection Examples

**Super Admin Only**:
```php
Route::post('/admin/shops', [ShopController::class, 'store'])
    ->middleware('super_admin');
```

**Admin or Super Admin**:
```php
Route::prefix('admin')
    ->middleware(['auth:sanctum', 'admin'])
    ->group(function () {
        // All admin routes
    });
```

**Customer Only** (public routes):
```php
Route::post('/orders', [OrderController::class, 'store'])
    ->middleware('auth:sanctum');
```

## Shop Context & Role Isolation

### How It Works

1. **Request arrives** → DetectShop middleware identifies shop from domain/query param
2. **Shop context set** → ShopContext service stores current shop
3. **User authenticates** → User loaded from database with their role
4. **Authorization checked** → Middleware validates user role/permissions
5. **Data filtered** → All queries filtered by shop_id (for admins) or user_id (for customers)

### Example: Admin accessing their shop

```php
// In AdminController
$shopId = ShopContext::getShopId();
$products = Product::where('shop_id', $shopId)->get();
// Only gets products from their shop
```

### Example: Super admin accessing all shops

```php
// In ShopController
$shops = Shop::all();  // Gets all shops
// Can filter and manage any shop
```

## Security Considerations

### Data Isolation
- ✅ Each shop's data is isolated by shop_id
- ✅ Admins only see their shop's data
- ✅ Customers only see public data and own orders
- ✅ Super admin can see all shops

### No Public Admin Registration
- ✅ Only super admin can create admin users
- ✅ Public API rejects admin/super_admin role in registration
- ✅ Prevents unauthorized admin access

### No Shop Creation by Regular Admins
- ✅ Only super admin can create shops
- ✅ Regular admins cannot access /api/admin/shops endpoints
- ✅ ShopController has super_admin middleware

### Token-Based Auth
- ✅ Uses Laravel Sanctum for token-based API auth
- ✅ Tokens are short-lived and revocable
- ✅ All API requests require authentication (except public endpoints)

## Database Structure

### Users Table
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY,
    shop_id BIGINT,                    -- NULL for super_admin, required for admin/customer
    phone VARCHAR(20) UNIQUE,          -- Phone number for OTP login
    email VARCHAR(255) UNIQUE,         -- Email address
    name VARCHAR(255),                 -- User name
    role ENUM('super_admin', 'admin', 'customer'),  -- Role
    is_active BOOLEAN DEFAULT true,    -- Active flag
    phone_verified BOOLEAN DEFAULT false,
    phone_verified_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP               -- Soft deletes
);

-- Indexes
KEY shop_id (shop_id),
KEY phone (phone),
KEY email (email),
KEY role (role)
```

### Key Points
- Super admin has `shop_id = NULL` (not tied to any shop)
- Admin users have `shop_id` pointing to their shop
- Customer users have `shop_id` pointing to shop they're registered on
- `phone` is unique globally (one phone = one user across all shops)
- `email` is unique globally

## Common Workflows

### Setup: Creating a New Shop with Admin

1. **Super admin creates shop**
   ```
   POST /api/admin/shops
   ```

2. **Super admin creates admin user for shop**
   ```
   POST /api/admin/admin-users
   {
       "phone": "+441234567892",
       "email": "admin@newshop.com",
       "shop_id": 2
   }
   ```

3. **Admin logs in** with their phone
   ```
   POST /api/auth/send-otp → +441234567892
   POST /api/auth/verify-otp
   ```

4. **Admin creates products**
   ```
   POST /api/admin/products
   ```

5. **Customers browse and order**
   ```
   GET /api/products
   POST /api/orders
   ```

### Admin Management Workflow

**View all admins**:
```
GET /api/admin/admin-users
```

**View admins for specific shop**:
```
GET /api/admin/admin-users?shop_id=2
```

**Update admin**:
```
PATCH /api/admin/admin-users/3
{
    "name": "Updated Name",
    "email": "newemail@example.com",
    "is_active": true
}
```

**Deactivate admin**:
```
PATCH /api/admin/admin-users/3
{
    "is_active": false
}
```

**Delete admin**:
```
DELETE /api/admin/admin-users/3
```

## Helper Methods in User Model

```php
$user->isSuperAdmin()      // bool
$user->isAdmin()           // bool
$user->isCustomer()        // bool
$user->canManageAdmin()    // bool (true if super_admin)
$user->canManageShop()     // bool (true if super_admin)
```

Usage in code:
```php
if (auth()->user()->isSuperAdmin()) {
    // Show super admin UI
}

if (auth()->user()->isAdmin()) {
    // Show admin UI
}
```

## Future Enhancements

Possible improvements:
- [ ] Role-based menu/UI (only show permitted options)
- [ ] Permission-based access (granular permissions per role)
- [ ] Admin invite workflow (send email invitation)
- [ ] Password-based auth option (in addition to OTP)
- [ ] API key authentication (for integrations)
- [ ] Activity logging (track who did what and when)
