# Server Management Scripts

## Starting the Server

To start the Laravel development server in background mode:

```bash
./start.sh
```

The server will:
- Clear all Laravel caches
- Check if a server is already running
- Start on `http://0.0.0.0:8000` in background mode
- Log output to `/tmp/laravel-server.log`

## Stopping the Server

To stop the running server:

```bash
./stop.sh
```

Or manually:

```bash
pkill -f "php artisan serve"
```

## Viewing Server Logs

To view real-time server logs:

```bash
tail -f /tmp/laravel-server.log
```

To view all logs:

```bash
cat /tmp/laravel-server.log
```

## Checking Server Status

To check if the server is running:

```bash
pgrep -f "php artisan serve"
```

If the server is running, this will show the process ID.

## Benefits of Background Mode

✅ **Persistent**: Server keeps running even when you run other commands
✅ **Non-blocking**: Terminal is immediately available for other tasks
✅ **Auto-restart**: Run `./start.sh` again to restart the server
✅ **Easy monitoring**: Logs are centralized in one file

## Troubleshooting

### Server won't start
Check the logs for errors:
```bash
cat /tmp/laravel-server.log
```

### Port already in use
Stop all PHP processes:
```bash
pkill -9 php
./start.sh
```

### Clear everything and restart
```bash
./stop.sh
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
./start.sh
```
