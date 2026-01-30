#!/bin/bash

# Laravel Development Server Start Script

echo "🚀 Starting ABC Grocery Shop Laravel Server..."

# Clear Laravel cache
echo "📦 Clearing Laravel cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Check if server is already running
if pgrep -f "php artisan serve" > /dev/null; then
    echo "⚠️  Server is already running!"
    echo "   To stop it, run: pkill -f 'php artisan serve'"
    exit 0
fi

# Start the Laravel development server in background
echo "🌐 Starting server on http://0.0.0.0:8000 (background mode)"
nohup php artisan serve --host=0.0.0.0 --port=8000 > /tmp/laravel-server.log 2>&1 &

# Wait a moment and check if it started
sleep 2

if pgrep -f "php artisan serve" > /dev/null; then
    echo "✅ Server started successfully!"
    echo "   View logs: tail -f /tmp/laravel-server.log"
    echo "   Stop server: pkill -f 'php artisan serve'"
else
    echo "❌ Failed to start server. Check logs: cat /tmp/laravel-server.log"
    exit 1
fi
