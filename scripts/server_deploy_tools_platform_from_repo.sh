#!/usr/bin/env bash

set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
APP_SRC="$REPO_ROOT/tools-platform"
APP_DST="${APP_DST:-/var/www/calclyo.com/platform}"
APP_NAME="${APP_NAME:-Calclyo}"
APP_URL="${APP_URL:-https://calclyo.com}"
DB_NAME="${DB_NAME:-calclyo_tools}"
DB_USER="${DB_USER:-calclyo_tools}"
DB_PASSWORD="${DB_PASSWORD:-change_me_now}"

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

php -r "file_exists('.env') || exit(1);"
if ! grep -q '^APP_KEY=' .env || grep -q '^APP_KEY=$' .env; then
  php artisan key:generate --force
fi

sed -i "s#^APP_NAME=.*#APP_NAME='${APP_NAME}'#" .env
sed -i "s#^APP_ENV=.*#APP_ENV=production#" .env
sed -i "s#^APP_DEBUG=.*#APP_DEBUG=false#" .env
sed -i "s#^APP_URL=.*#APP_URL=${APP_URL}#" .env
grep -q '^APP_LOCALE=' .env || printf '\nAPP_LOCALE=ar\n' >> .env
grep -q '^APP_FALLBACK_LOCALE=' .env || printf 'APP_FALLBACK_LOCALE=ar\n' >> .env
grep -q '^DB_CONNECTION=' .env || printf 'DB_CONNECTION=mysql\n' >> .env
grep -q '^DB_HOST=' .env || printf 'DB_HOST=127.0.0.1\n' >> .env
grep -q '^DB_PORT=' .env || printf 'DB_PORT=3306\n' >> .env
grep -q '^DB_DATABASE=' .env || printf 'DB_DATABASE=\n' >> .env
grep -q '^DB_USERNAME=' .env || printf 'DB_USERNAME=\n' >> .env
grep -q '^DB_PASSWORD=' .env || printf 'DB_PASSWORD=\n' >> .env
sed -i "s#^DB_CONNECTION=.*#DB_CONNECTION=mysql#" .env
sed -i "s#^DB_HOST=.*#DB_HOST=127.0.0.1#" .env
sed -i "s#^DB_PORT=.*#DB_PORT=3306#" .env
sed -i "s#^DB_DATABASE=.*#DB_DATABASE=${DB_NAME}#" .env
sed -i "s#^DB_USERNAME=.*#DB_USERNAME=${DB_USER}#" .env
sed -i "s#^DB_PASSWORD=.*#DB_PASSWORD=${DB_PASSWORD}#" .env

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

echo "Git-based deployment completed for calclyo.com"
