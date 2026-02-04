# Build Instructions

## Quick Start

To build and run the Madina Halal Shop application:

```bash
# 1. Install dependencies
composer install
npm install

# 2. Set up environment
cp .env.example .env
php artisan key:generate

# 3. Create database
touch database/database.sqlite

# 4. Run migrations
php artisan migrate

# 5. Build assets
npm run build

# 6. Start server
php artisan serve
```

## Build Steps Explained

### Step 1: Install PHP Dependencies
```bash
composer install
```
This installs all required PHP packages including Laravel, AWS SDK, Twilio SDK, etc.

### Step 2: Install Node Dependencies
```bash
npm install
```
This installs frontend dependencies for Vite and asset compilation.

### Step 3: Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```
Creates your environment configuration file and generates a secure application key.

### Step 4: Database Setup
```bash
touch database/database.sqlite
php artisan migrate
```
Creates the SQLite database file and runs all migrations to create the database schema.

### Step 5: Storage Setup
```bash
php artisan storage:link
```
Creates a symbolic link from public/storage to storage/app/public for file access.

### Step 6: Build Frontend Assets

For development:
```bash
npm run dev
```

For production:
```bash
npm run build
```

### Step 7: Run the Application

Development server:
```bash
php artisan serve
```

The application will be available at http://localhost:8000

## Production Build

For production deployment:

```bash
# Install dependencies (production only)
composer install --optimize-autoloader --no-dev
npm install --production

# Build assets
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Set permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Docker Build (Optional)

If you prefer to use Docker:

```bash
# Build image
docker build -t madina-shop .

# Run container
docker run -p 8000:8000 madina-shop
```

## Common Issues

### Permission Errors
```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:www-data storage bootstrap/cache
```

### Database Connection
Ensure database/database.sqlite exists and is writable:
```bash
touch database/database.sqlite
chmod 664 database/database.sqlite
```

### Missing Dependencies
If you encounter missing class errors:
```bash
composer dump-autoload
php artisan clear-compiled
php artisan optimize
```