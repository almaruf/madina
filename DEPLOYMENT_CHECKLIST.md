# Deployment Checklist - Multi-Tenant Platform

## Pre-Deployment Setup

### 1. Environment Configuration
- [ ] Copy `.env.example` to `.env`
- [ ] Set `APP_KEY`: `php artisan key:generate`
- [ ] Configure `APP_URL` (production URL)
- [ ] Set `APP_DEBUG=false` (production)
- [ ] Configure database connection (MySQL/PostgreSQL)

### 2. Service Credentials
- [ ] **AWS S3**: `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`
- [ ] **Twilio**: `TWILIO_SID`, `TWILIO_TOKEN`, `TWILIO_FROM`
- [ ] **AWS SES** (optional): Email configuration

### 3. Database Setup
- [ ] Create database: `php artisan migrate`
- [ ] Seed initial shop: `php artisan db:seed`
- [ ] Verify shops table created with `is_active` column
- [ ] Verify all tenant tables have `shop_id` foreign keys

### 4. Domain Configuration
- [ ] DNS records configured for shop domains
  ```
  Example:
  shop1.example.com → your.server.ip
  shop2.example.com → your.server.ip
  myshop.example.com → your.server.ip
  ```
- [ ] SSL certificates installed (wildcard or per-domain)
- [ ] Web server (nginx/Apache) configured for domain routing

### 5. Laravel Configuration
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Run `php artisan optimize`

### 6. Storage & Permissions
- [ ] `php artisan storage:link` (if using local storage)
- [ ] Set proper permissions:
  ```bash
  chmod -R 775 storage bootstrap/cache
  chown -R www-data:www-data storage bootstrap
  ```

### 7. Frontend Assets
- [ ] Run `npm install`
- [ ] Run `npm run build` (production build)
- [ ] Verify assets compiled in `public/build/`

### 8. Application Setup
- [ ] Create first shop(s) via admin API or database seeding
- [ ] Test shop detection (domain-based routing)
- [ ] Create admin users for each shop
- [ ] Test login with phone OTP

## Deployment Verification

### 1. Shop Detection
- [ ] Access shop1 via its domain
- [ ] Access shop2 via its domain
- [ ] Verify correct shop data displayed
- [ ] Verify shop isolation (no data leakage)

### 2. Authentication
- [ ] Phone-based OTP login works
- [ ] Admin can access shop management endpoints
- [ ] Customer can place orders
- [ ] Users only see their own shop's data

### 3. Product Management
- [ ] Admin can create products for their shop
- [ ] Products only appear in correct shop
- [ ] Product variations work
- [ ] Images upload to S3 correctly

### 4. Order Management
- [ ] Customers can place orders
- [ ] Orders appear in correct shop admin
- [ ] Order notifications sent via email/SMS
- [ ] Stock is updated correctly

### 5. API Endpoints
- [ ] `POST /api/auth/send-otp` - Works
- [ ] `POST /api/auth/verify-otp` - Returns token
- [ ] `GET /api/products` - Returns shop products
- [ ] `GET /api/admin/shops` - Lists shops (admin)
- [ ] `GET /api/admin/products` - Returns shop products (admin)
- [ ] `GET /api/admin/orders` - Returns shop orders (admin)

### 6. Multi-Tenancy
- [ ] Shop context correctly detected per request
- [ ] Data isolation verified (no cross-shop data)
- [ ] Cache clearing works on shop updates
- [ ] Admin endpoints respect shop boundaries

## Production Deployment Steps

### Step 1: Prepare Server
```bash
# Install dependencies
composer install --no-dev --optimize-autoloader
npm install --production
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Step 2: Setup Database
```bash
# Run migrations
php artisan migrate --force

# Seed initial data
php artisan db:seed
```

### Step 3: Create Initial Shops
**Option A: Via Database**
```bash
php artisan tinker
>>> App\Models\Shop::create(['name' => 'Shop 1', 'slug' => 'shop-1', 'domain' => 'shop1.example.com', ...])
>>> App\Models\User::create(['shop_id' => 1, 'phone' => '+441234567890', 'role' => 'admin', ...])
```

**Option B: Via Admin API**
```bash
curl -X POST https://shop1.example.com/api/admin/shops \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{...shop data...}'
```

### Step 4: Configure Web Server

**Nginx Example:**
```nginx
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name *.example.com;
    
    ssl_certificate /path/to/wildcard.crt;
    ssl_certificate_key /path/to/wildcard.key;
    
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

# Redirect HTTP to HTTPS
server {
    listen 80;
    listen [::]:80;
    server_name *.example.com;
    return 301 https://$server_name$request_uri;
}
```

**Apache Example:**
```apache
<VirtualHost *:443>
    ServerName *.example.com
    DocumentRoot /var/www/app/public
    
    <Directory /var/www/app/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
        
        <IfModule mod_rewrite.c>
            RewriteEngine On
            RewriteBase /
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteRule ^ index.php [QSA,L]
        </IfModule>
    </Directory>
    
    <FilesMatch \.php$>
        SetHandler "proxy:unix:/run/php/php8.2-fpm.sock|fcgi://localhost"
    </FilesMatch>
    
    SSLEngine on
    SSLCertificateFile /path/to/wildcard.crt
    SSLCertificateKeyFile /path/to/wildcard.key
</VirtualHost>

<VirtualHost *:80>
    ServerName *.example.com
    Redirect permanent / https://%{HTTP_HOST}%{REQUEST_URI}
</VirtualHost>
```

### Step 5: Security Hardening
- [ ] Enable HTTPS with SSL certificates
- [ ] Set strong app key: `php artisan key:generate`
- [ ] Configure CORS if needed
- [ ] Set up rate limiting on auth endpoints
- [ ] Enable Laravel's built-in security headers
- [ ] Configure firewall/WAF rules
- [ ] Enable database backups (daily/hourly)
- [ ] Monitor error logs regularly

### Step 6: Monitoring & Logging
- [ ] Configure Laravel logging (Sentry, DataDog, etc.)
- [ ] Set up monitoring for API endpoints
- [ ] Monitor database performance
- [ ] Track AWS S3/SES usage
- [ ] Monitor Twilio SMS delivery
- [ ] Set up alerts for errors/failures

## Post-Deployment

### 1. Verify All Systems
- [ ] Test all shops are accessible
- [ ] Verify SSL certificates valid
- [ ] Check database backups running
- [ ] Confirm email/SMS notifications working
- [ ] Verify file uploads to S3

### 2. Create Admin Documentation
- [ ] How to create new shops
- [ ] How to manage domains
- [ ] How to handle support requests
- [ ] Deployment rollback procedures

### 3. Monitor Deployment
- [ ] First 24 hours: Monitor for errors
- [ ] Check slow queries in database
- [ ] Monitor AWS costs
- [ ] Track payment processing
- [ ] Monitor customer feedback

## Scaling Considerations

### Horizontal Scaling
- [ ] Database: Use read replicas for scaling reads
- [ ] Cache: Redis for session/query caching
- [ ] Queue: Set up Laravel queues for background jobs
- [ ] Load Balancer: Distribute traffic across servers

### Database Optimization
- [ ] Add indexes on frequently queried columns (shop_id, user_id, etc.)
- [ ] Monitor slow queries with `php artisan tinker`
- [ ] Archive old orders periodically
- [ ] Use database pagination for large result sets

### API Performance
- [ ] Implement API caching (Redis)
- [ ] Use eager loading for relationships
- [ ] Add query pagination
- [ ] Monitor N+1 query problems

## Backup & Recovery

### Daily Backups
```bash
# Database backup
mysqldump -u user -p database > /backups/db_$(date +%Y%m%d).sql

# Application files
tar -czf /backups/app_$(date +%Y%m%d).tar.gz /var/www/app
```

### Recovery Steps
```bash
# Restore database
mysql -u user -p database < /backups/db_YYYYMMDD.sql

# Restore files
tar -xzf /backups/app_YYYYMMDD.tar.gz
```

## Support & Maintenance

### Regular Maintenance
- [ ] Update Laravel and dependencies monthly
- [ ] Review and optimize database queries
- [ ] Monitor server resources (CPU, RAM, disk)
- [ ] Review error logs weekly
- [ ] Test backup restoration procedures

### Security Updates
- [ ] Monitor Laravel security advisories
- [ ] Update dependencies immediately
- [ ] Run `composer update` in staging first
- [ ] Test thoroughly before deploying to production

## Rollback Procedures

### Quick Rollback (If Issues Found)
```bash
# 1. Revert to previous commit
git checkout <previous-commit>

# 2. Clear caches
php artisan config:clear
php artisan cache:clear

# 3. Restart queue workers
supervisorctl restart all

# 4. Monitor logs
tail -f storage/logs/laravel.log
```

### Database Rollback
```bash
# Rollback last migration
php artisan migrate:rollback

# Or rollback to specific step
php artisan migrate:rollback --step=5
```

## Success Criteria

✅ All shops accessible via their domains
✅ No data leakage between shops
✅ Authentication/OTP working correctly
✅ Products, orders, and admin features working
✅ Email/SMS notifications functioning
✅ Images uploading to S3 successfully
✅ API endpoints returning correct data
✅ Performance acceptable (< 2s response time)
✅ Error rate < 0.1%
✅ All security checks passed

## Contacts & Escalation

- **Technical Issues**: Development team
- **Database Issues**: Database administrator
- **Infrastructure**: DevOps team
- **Security**: Security team
- **Customer Support**: Support team

---

**Deployment Date**: ___________
**Deployed By**: ___________
**Sign-off**: ___________
