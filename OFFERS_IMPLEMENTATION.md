# Offers & Promotions System Implementation

## Overview
Complete implementation of a flexible offers/promotions system for the ABC Grocery Shop multi-tenant platform. The system supports 7 different offer types with full admin management and customer-facing display.

## Offer Types Supported

1. **Percentage Discount** - e.g., "20% off selected items"
2. **Fixed Discount** - e.g., "£2 off selected products"
3. **Buy X Get Y Free (BOGO)** - e.g., "Buy 2 Get 1 Free"
4. **Multi-Buy Deal** - e.g., "3 for £10"
5. **Buy X Get Y at Discount** - e.g., "Buy 2 Get 50% off the third"
6. **Flash Sale** - Time-limited percentage or fixed discounts
7. **Bundle Deal** - Special bundle pricing for product combinations

## Database Schema

### `offers` Table
- `id` - Primary key
- `shop_id` - Foreign key to shops (multi-tenancy)
- `name` - Offer name
- `description` - Optional description
- `type` - Enum (percentage_discount, fixed_discount, bxgy_free, multibuy, bxgy_discount, flash_sale, bundle)
- `discount_value` - Percentage or fixed amount
- `buy_quantity` - For BXGY and multibuy offers
- `get_quantity` - For BXGY offers
- `get_discount_percentage` - For BXGY discount offers
- `bundle_price` - For bundle offers
- `starts_at` - Start datetime (nullable)
- `ends_at` - End datetime (nullable)
- `badge_text` - Display text for badge (e.g., "20% OFF")
- `badge_color` - Hex color for badge (default: #DC2626)
- `min_purchase_amount` - Minimum cart value to qualify
- `max_uses_per_customer` - Usage limit per customer
- `total_usage_limit` - Total usage limit across all customers
- `current_usage_count` - Tracking counter
- `priority` - Display order (higher = shown first)
- `is_active` - Active/inactive status
- `timestamps`

### `offer_product` Pivot Table
- `offer_id` - Foreign key to offers
- `product_id` - Foreign key to products
- Defines which products belong to each offer

## Features Implemented

### Admin Interface (`/admin/offers`)

#### Offer Management
- **List View**: Displays all offers with filters
  - Filter tabs: All / Active / Inactive / Expired
  - Offer cards showing: name, type, description, dates, status, usage stats
  - Quick actions: Edit, Toggle Status, Delete, Manage Products

- **Create/Edit Modal**: Comprehensive form with:
  - Basic info: Name, Type, Description
  - Dynamic fields based on offer type
  - Date range selection (starts_at, ends_at)
  - Badge customization (text and color picker)
  - Conditions: Min purchase, usage limits
  - Priority and active status
  - Real-time field updates based on selected type

- **Product Assignment**: Dedicated modal for managing products
  - Two-column layout:
    - Left: Available products (searchable)
    - Right: Products currently in offer
  - One-click add/remove functionality
  - Product search capability
  - Visual feedback with product images and prices

#### Navigation
- Added "Offers" link in admin sidebar (between Categories and Users)
- Icon: `fa-tags`
- Active state highlighting

### Customer-Facing Display (`/`)

#### Home Page Offers Section
- Positioned after banners, before featured categories
- Displays active offers with their products
- Product cards show:
  - Offer badge (customizable text and color)
  - Product image with hover zoom effect
  - Original and discounted prices
  - Savings amount
  - Offer description
  - Red "Add to Cart" button
- "View All Offers" button linking to filtered products page

#### Product Cards Features
- Offer badge overlay (top-right corner)
- Price comparison display
- Savings calculation
- Visual distinction (red color scheme for offer products)

### API Endpoints

#### Admin Routes (`/api/admin/offers`)
Protected with `auth:sanctum` and `admin` middleware:

- `GET /api/admin/offers` - List offers (with filters)
- `POST /api/admin/offers` - Create offer
- `GET /api/admin/offers/{id}` - Get offer details
- `PUT /api/admin/offers/{id}` - Update offer
- `DELETE /api/admin/offers/{id}` - Delete offer
- `POST /api/admin/offers/{id}/toggle-status` - Toggle active status
- `GET /api/admin/offers/{id}/products` - Get products in offer
- `POST /api/admin/offers/{id}/products` - Add product to offer
- `DELETE /api/admin/offers/{id}/products/{productId}` - Remove product from offer

#### Public Routes
- `GET /api/offers/active` - Get all active offers (for frontend)

## Models & Relationships

### Offer Model (`app/Models/Offer.php`)
**Relationships:**
- `shop()` - belongsTo Shop
- `products()` - belongsToMany Product

**Scopes:**
- `active()` - Filter active offers
- `valid()` - Filter by date validity
- `forShop($shopId)` - Filter by shop

**Key Methods:**
- `isValid()` - Check if offer is currently valid
- `calculateDiscount($originalPrice, $quantity)` - Calculate discount amount
- `getDiscountedPrice($originalPrice, $quantity)` - Get final discounted price

### Product Model Updates
**New Methods:**
- `offers()` - belongsToMany Offer
- `activeOffers()` - Get currently active offers
- `getBestOffer()` - Get best applicable offer
- `hasActiveOffer()` - Check if product has active offer

## Discount Calculation Logic

### Percentage Discount
```php
$discount = ($originalPrice * $discountValue) / 100;
```

### Fixed Discount
```php
$discount = $discountValue;
```

### Buy X Get Y Free
```php
$setsEligible = floor($quantity / ($buyQty + $getQty));
$discount = $originalPrice * $getQty * $setsEligible;
```

### Multi-Buy
```php
$setsEligible = floor($quantity / $buyQty);
$discount = ($originalPrice * $buyQty - $bundlePrice) * $setsEligible;
```

### Buy X Get Y at Discount
```php
$setsEligible = floor($quantity / ($buyQty + $getQty));
$discount = ($originalPrice * $getDiscountPct / 100) * $getQty * $setsEligible;
```

## Multi-Tenancy Support

All offers are automatically scoped to the current shop via:
- `shop_id` foreign key in offers table
- `ShopContext::getShopId()` used in all queries
- Product assignment validates products belong to same shop
- Automatic shop assignment on offer creation

## Usage Examples

### Creating a Percentage Discount Offer
```javascript
{
  name: "Summer Sale",
  description: "20% off selected items",
  type: "percentage_discount",
  discount_value: 20,
  badge_text: "20% OFF",
  badge_color: "#DC2626",
  starts_at: "2024-06-01 00:00:00",
  ends_at: "2024-08-31 23:59:59",
  is_active: true
}
```

### Creating a BOGO Offer
```javascript
{
  name: "Buy 2 Get 1 Free",
  description: "Mix and match on selected products",
  type: "bxgy_free",
  buy_quantity: 2,
  get_quantity: 1,
  badge_text: "B2G1",
  badge_color: "#16A34A",
  is_active: true
}
```

### Assigning Products to Offer
```bash
# Add product to offer
POST /api/admin/offers/1/products
{ "product_id": 5 }

# Remove product from offer
DELETE /api/admin/offers/1/products/5
```

## Testing Checklist

### Admin Interface
- [ ] Can create offers of all types
- [ ] Dynamic form fields update based on type
- [ ] Can edit existing offers
- [ ] Can toggle offer status (active/inactive)
- [ ] Can delete offers
- [ ] Filter tabs work correctly
- [ ] Product assignment modal opens
- [ ] Can add products to offer
- [ ] Can remove products from offer
- [ ] Product search works

### Frontend Display
- [ ] Offers section displays on home page
- [ ] Offer badges show correctly with custom text/color
- [ ] Discounted prices calculate correctly
- [ ] Savings amount displays
- [ ] Hover effects work on product images
- [ ] Add to cart works for offer products
- [ ] "View All Offers" link works

### Multi-Tenancy
- [ ] Offers isolated per shop
- [ ] Cannot see other shops' offers
- [ ] Cannot assign products from other shops
- [ ] ShopContext correctly identifies current shop

### Discount Calculations
- [ ] Percentage discounts calculate correctly
- [ ] Fixed discounts apply properly
- [ ] BOGO offers work with correct quantity logic
- [ ] Multi-buy deals calculate savings
- [ ] BXGY discount applies correct percentage
- [ ] Date validity checks work
- [ ] Usage limits enforced

## Future Enhancements

1. **Cart Integration** (Phase 4 - Not Yet Implemented)
   - Auto-apply offers in cart
   - Show savings in cart summary
   - Validate offer conditions at checkout

2. **Offer Analytics**
   - Track offer performance
   - Revenue impact analysis
   - Most popular offers report

3. **Advanced Offer Types**
   - Coupon codes
   - First-time customer offers
   - Referral discounts
   - Tiered discounts (spend more, save more)

4. **Customer Notifications**
   - Email alerts for new offers
   - Push notifications for flash sales
   - Personalized offer recommendations

5. **Offer Stacking**
   - Allow/restrict combining multiple offers
   - Priority-based offer selection
   - Maximum discount caps

## Files Modified/Created

### Created Files
- `database/migrations/2024_01_01_000015_create_offers_table.php`
- `app/Models/Offer.php`
- `app/Http/Controllers/Admin/OfferController.php`
- `resources/views/admin/offers.blade.php`
- `OFFERS_IMPLEMENTATION.md` (this file)

### Modified Files
- `app/Models/Product.php` - Added offer relationships
- `app/Http/Controllers/Api/ProductController.php` - Added activeOffers() method
- `app/Http/Controllers/Shop/HomeController.php` - Load offers for home page
- `routes/api.php` - Added offer routes
- `routes/web.php` - Added admin offers page route
- `resources/views/admin/layout.blade.php` - Added Offers nav link
- `resources/views/shop/index.blade.php` - Added offers section display

## Deployment Notes

1. Run migration: `php artisan migrate`
2. Clear caches: `php artisan config:clear && php artisan cache:clear`
3. Ensure proper permissions on storage directories
4. Test offer creation and display
5. Verify multi-tenancy isolation
6. Test discount calculations

## Support & Documentation

For questions or issues related to the offers system:
- Review this documentation
- Check `app/Models/Offer.php` for discount calculation logic
- See `resources/views/admin/offers.blade.php` for UI examples
- Refer to `.github/copilot-instructions.md` for project architecture

---

**Implementation Status**: ✅ Complete (Phases 1-3)
**Last Updated**: 2024
**Version**: 1.0
