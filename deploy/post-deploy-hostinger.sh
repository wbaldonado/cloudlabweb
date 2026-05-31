#!/usr/bin/env bash
# Run on Hostinger via SSH after upload (project root = folder with artisan)
set -euo pipefail

cd "$(dirname "$0")/.." || exit 1
echo "=== Post-deploy (Hostinger) — $(pwd) ==="

if [ ! -f artisan ]; then
  echo "ERROR: artisan not found. cd to Laravel root first."
  exit 1
fi

if [ ! -f .env ]; then
  echo "ERROR: .env missing. Copy .env.hostinger.example to .env and edit."
  exit 1
fi

if ! grep -q 'APP_KEY=base64:' .env 2>/dev/null; then
  echo "Generating APP_KEY..."
  php artisan key:generate --force
fi

composer install --no-dev --optimize-autoloader --no-interaction 2>/dev/null || true

mkdir -p storage/framework/{sessions,views,cache/data} storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

php artisan storage:link 2>/dev/null || echo "WARN: storage:link failed (create link in hPanel or copy storage/app/public to public/storage)"

php artisan migrate --force

php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Done. Test your site and admin. ==="
