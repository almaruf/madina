#!/bin/bash

# Laravel Development Server Stop Script

echo "🛑 Stopping ABC Grocery Shop Laravel Server..."

if pgrep -f "php artisan serve" > /dev/null; then
    pkill -f "php artisan serve"
    sleep 1
    
    if pgrep -f "php artisan serve" > /dev/null; then
        echo "⚠️  Failed to stop server gracefully, forcing..."
        pkill -9 -f "php artisan serve"
    fi
    
    echo "✅ Server stopped successfully!"
else
    echo "⚠️  Server is not running."
fi
