# Shop Configuration Guide

## Overview
This application is designed to be a **multi-tenant** platform that can be easily rebranded and configured for different grocery shops without modifying code.

## Configuration File: `config/shop.json`

### Complete Configuration Schema

```json
{
    "shop_name": "Your Shop Name",
    "shop_slug": "your-shop-slug",
    "description": "Your shop description",
    "tagline": "Your catchy tagline",
    
    "location": {
        "address": "123 Main Street",
        "city": "London",
        "postcode": "SW1A 1AA",
        "country": "United Kingdom"
    },
    
    "contact": {
        "phone": "+44 20 1234 5678",
        "email": "info@yourshop.com",
        "support_email": "support@yourshop.com"
    },
    
    "business": {
        "business_type": "grocery",
        "specialization": "general|halal|organic|asian|african|caribbean",
        "has_halal_products": true,
        "has_organic_products": false,
        "has_international_products": false
    },
    
    "features": {
        "delivery_enabled": true,
        "collection_enabled": true,
        "online_payment": false,
        "loyalty_program": false,
        "reviews_enabled": false
    },
    
    "delivery": {
        "radius_km": 10,
        "min_order_amount": 20.00,
        "delivery_fee": 3.99,
        "free_delivery_threshold": 50.00,
        "currency": "GBP",
        "currency_symbol": "£"
    },
    
    "branding": {
        "primary_color": "#10b981",
        "secondary_color": "#059669",
        "logo_url": null,
        "favicon_url": null
    },
    
    "social_media": {
        "facebook": "https://facebook.com/yourshop",
        "instagram": "https://instagram.com/yourshop",
        "twitter": "https://twitter.com/yourshop",
        "whatsapp": "+44 20 1234 5678"
    },
    
    "operating_hours": {
        "monday": "09:00-20:00",
        "tuesday": "09:00-20:00",
        "wednesday": "09:00-20:00",
        "thursday": "09:00-20:00",
        "friday": "09:00-20:00",
        "saturday": "09:00-18:00",
        "sunday": "10:00-16:00"
    }
}
```

## Setting Up a New Shop

### Step 1: Edit Shop Configuration
```bash
# Open and edit the shop configuration file
nano config/shop.json
```

Update all relevant fields for your shop.

### Step 2: Update Environment Variables
```bash
# Copy environment template if not exists
cp .env.example .env

# Edit environment file
nano .env
```

Update these key variables:
```env
APP_NAME="Your Shop Name"
SHOP_PHONE="+44 20 1234 5678"
SHOP_EMAIL="info@yourshop.com"
```

### Step 3: Configure Services
If you need AWS S3, SES, or Twilio:
```env
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_BUCKET=your_bucket

TWILIO_SID=your_sid
TWILIO_TOKEN=your_token
TWILIO_FROM=your_phone
```

### Step 4: Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
```

### Step 5: Verify
Visit your application and verify:
- Shop name displays correctly
- Contact information is correct
- Branding colors are applied
- Features are enabled/disabled as configured

## Using Configuration in Code

### In Controllers
```php
use App\Services\ShopConfigService;

class MyController extends Controller
{
    protected $shopConfig;

    public function __construct(ShopConfigService $shopConfig)
    {
        $this->shopConfig = $shopConfig;
    }

    public function index()
    {
        $shopName = $this->shopConfig->name();
        $hasHalal = $this->shopConfig->hasHalalProducts();
        
        // Use configuration
    }
}
```

### In Blade Views
```blade
<h1>{{ app(\App\Services\ShopConfigService::class)->name() }}</h1>
<p>{{ app(\App\Services\ShopConfigService::class)->description() }}</p>
<p>Call us: {{ app(\App\Services\ShopConfigService::class)->phone() }}</p>
```

### Available Methods
```php
$config = app(ShopConfigService::class);

// Basic info
$config->name()          // Shop name
$config->slug()          // Shop slug
$config->description()   // Shop description
$config->tagline()       // Tagline

// Contact
$config->phone()         // Phone number
$config->email()         // Email address
$config->address()       // Street address
$config->fullAddress()   // Full formatted address

// Business
$config->hasHalalProducts()        // Check if halal products
$config->isFeatureEnabled('delivery')  // Check feature

// Delivery
$config->currency()                // Currency code
$config->currencySymbol()          // Currency symbol
$config->deliveryConfig()          // All delivery settings

// Branding
$config->primaryColor()            // Primary brand color

// Operating hours
$config->operatingHours('monday')  // Get specific day
$config->operatingHours()          // Get all days

// Raw access
$config->get('social_media.facebook')  // Get any nested value
```

## Common Configurations

### Halal Grocery Shop
```json
{
    "shop_name": "Madina Halal Shop",
    "specialization": "halal",
    "has_halal_products": true,
    "has_organic_products": false
}
```

### Organic Grocery Shop
```json
{
    "shop_name": "Green Valley Organics",
    "specialization": "organic",
    "has_halal_products": false,
    "has_organic_products": true
}
```

### General Grocery Shop
```json
{
    "shop_name": "ABC Grocery Shop",
    "specialization": "general",
    "has_halal_products": false,
    "has_organic_products": false
}
```

### Asian Grocery Shop
```json
{
    "shop_name": "Oriental Express",
    "specialization": "asian",
    "has_international_products": true
}
```

## Feature Flags

Feature flags control what functionality is available:

- **delivery_enabled**: Shows delivery options
- **collection_enabled**: Shows collection/pickup options
- **online_payment**: Enables online payment methods
- **loyalty_program**: Shows loyalty points and rewards
- **reviews_enabled**: Allows customers to leave reviews

Example usage in code:
```php
if ($shopConfig->isFeatureEnabled('loyalty_program')) {
    // Show loyalty points
}
```

## Branding Customization

### Colors
Update `branding.primary_color` and `branding.secondary_color` in shop.json. These can be used in your views.

### Logo & Favicon
1. Upload images to S3 or public storage
2. Update `branding.logo_url` and `branding.favicon_url` with full URLs

### Tailwind CSS
For custom colors, update `tailwind.config.js`:
```js
theme: {
    extend: {
        colors: {
            primary: '#your-color',
        }
    }
}
```

## Best Practices

1. **Don't hardcode**: Never hardcode shop names, addresses, or phone numbers
2. **Use feature flags**: Gate features behind configuration flags
3. **Test thoroughly**: After changing config, test all pages
4. **Backup**: Keep a backup of your shop.json before major changes
5. **Version control**: Commit shop.json changes with descriptive messages
6. **Documentation**: Document any custom configurations added

## Troubleshooting

### Configuration not updating
```bash
php artisan config:clear
php artisan cache:clear
```

### Invalid JSON error
Validate your JSON:
```bash
php -r "json_decode(file_get_contents('config/shop.json')); echo json_last_error_msg();"
```

### Missing configuration values
Check that all required fields are present in shop.json. The system will use defaults for missing optional fields.

## Security Notes

- **Never commit sensitive data** to shop.json (use .env instead)
- Shop.json is for public/semi-public information only
- Keep API keys, passwords, and secrets in .env
- .env should never be committed to version control