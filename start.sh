#!/bin/bash

# Laravel Development Server Start Script

echo "🚀 Starting ABC Grocery Shop Laravel Server..."

# Clear Laravel cache
echo "📦 Clearing Laravel cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Start the Laravel development server
echo "🌐 Starting server on http://0.0.0.0:8000"
php artisan serve --host=0.0.0.0 --port=8000
