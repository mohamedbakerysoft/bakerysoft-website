#!/usr/bin/env bash

set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
APP_SRC="$REPO_ROOT/tools-platform"
APP_DST="${APP_DST:-/var/www/tools.bakerysoft.net/platform}"

sudo mkdir -p "$APP_DST"
sudo chown -R "$(id -un)":"$(id -gn)" "$APP_DST"

rsync -av --delete \
  --exclude '.env' \
  --exclude 'vendor' \
  --exclude 'node_modules' \
  --exclude 'storage/logs/*' \
  "$APP_SRC/" "$APP_DST/"

cd "$APP_DST"

[ -f .env ] || cp .env.example .env

composer install --no-interaction --prefer-dist --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan db:seed --class=ToolsPlatformSeeder --force
php artisan storage:link || true
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

sudo chown -R www-data:www-data storage bootstrap/cache
sudo nginx -t
sudo systemctl reload nginx

echo "Git-based deployment completed for tools.bakerysoft.net"
