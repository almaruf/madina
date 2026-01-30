#!/bin/bash

# ABC Grocery Shop - Full Dev Environment Start Script

echo "🚀 Starting ABC Grocery Shop Development Environment..."

# Clear Laravel cache
echo "📦 Clearing Laravel cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Start Laravel server
if pgrep -f "php artisan serve" > /dev/null; then
    echo "⚠️  Laravel server is already running!"
else
    echo "🌐 Starting Laravel server on http://0.0.0.0:8000 (background mode)"
    nohup php artisan serve --host=0.0.0.0 --port=8000 > /tmp/laravel-server.log 2>&1 &
    sleep 2
    if pgrep -f "php artisan serve" > /dev/null; then
        echo "✅ Laravel server started! (logs: /tmp/laravel-server.log)"
    else
        echo "❌ Failed to start Laravel server. See /tmp/laravel-server.log"
        exit 1
    fi
fi

# Start Laravel queue worker
if pgrep -f "php artisan queue:work" > /dev/null; then
    echo "⚠️  Queue worker is already running!"
else
    echo "🧵 Starting Laravel queue worker (background mode)"
    nohup php artisan queue:work --tries=3 > /tmp/laravel-queue.log 2>&1 &
    sleep 2
    if pgrep -f "php artisan queue:work" > /dev/null; then
        echo "✅ Queue worker started! (logs: /tmp/laravel-queue.log)"
    else
        echo "❌ Failed to start queue worker. See /tmp/laravel-queue.log"
    fi
fi

# Start Vite dev server
if pgrep -f "vite" > /dev/null; then
    echo "⚠️  Vite dev server is already running!"
else
    echo "🎨 Starting Vite dev server (background mode)"
    nohup npm run dev > /tmp/vite-dev.log 2>&1 &
    sleep 2
    if pgrep -f "vite" > /dev/null; then
        echo "✅ Vite dev server started! (logs: /tmp/vite-dev.log)"
    else
        echo "❌ Failed to start Vite dev server. See /tmp/vite-dev.log"
    fi
fi

echo "\n🟢 All services started!"
echo "- Laravel:     http://localhost:8000  (logs: /tmp/laravel-server.log)"
echo "- Vite:        http://localhost:5173   (logs: /tmp/vite-dev.log)"
echo "- Queue logs:  /tmp/laravel-queue.log"

echo "\nTo stop all services, run:"
echo "  pkill -f 'php artisan serve'"
echo "  pkill -f 'php artisan queue:work'"
echo "  pkill -f 'vite'"
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
