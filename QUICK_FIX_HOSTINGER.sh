#!/bin/bash
# Quick Fix Script for Hostinger 500 Error
# Run this via SSH on Hostinger

echo "=== Hostinger Laravel 500 Error Fix ==="
echo ""

# Navigate to project directory (UPDATE THIS PATH)
cd /home/USERNAME/your-app-folder || exit

echo "1. Setting permissions..."
chmod -R 775 storage bootstrap/cache
chown -R $(whoami):$(whoami) storage bootstrap/cache

echo "2. Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo "3. Generating APP_KEY if needed..."
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    php artisan key:generate
    echo "   ✓ APP_KEY generated"
else
    echo "   ✓ APP_KEY already exists"
fi

echo "4. Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "5. Installing dependencies..."
composer install --no-dev --optimize-autoloader

echo ""
echo "=== Fix Complete ==="
echo ""
echo "If you still get 500 error, check logs:"
echo "tail -n 100 storage/logs/laravel.log"
echo ""

