#!/usr/bin/env bash

set -euo pipefail

REMOTE_HOST="${REMOTE_HOST:-jarvis@jarvis.local}"
REMOTE_APP_DIR="${REMOTE_APP_DIR:-/var/www/calclyo.com/platform}"
LOCAL_APP_DIR="${LOCAL_APP_DIR:-tools-platform/}"
LOCAL_NGINX_CONF="${LOCAL_NGINX_CONF:-deploy/nginx/calclyo.com.conf}"
DB_NAME="${DB_NAME:-calclyo_tools}"
DB_USER="${DB_USER:-calclyo_tools}"
DB_PASSWORD="${DB_PASSWORD:-change_me_now}"
APP_KEY="${APP_KEY:-}"

if [[ -z "$APP_KEY" ]]; then
  APP_KEY="$(php -r "require '$LOCAL_APP_DIR/vendor/autoload.php'; echo 'base64:'.base64_encode(random_bytes(32));")"
fi

ssh "$REMOTE_HOST" "sudo mkdir -p '$REMOTE_APP_DIR' && sudo chown -R \$(id -un):\$(id -gn) '$REMOTE_APP_DIR'"
rsync -av --delete --exclude '.env' --exclude 'vendor' --exclude 'node_modules' --exclude 'storage/logs/*' "$LOCAL_APP_DIR" "$REMOTE_HOST:$REMOTE_APP_DIR/"
scp "$LOCAL_NGINX_CONF" "$REMOTE_HOST:/tmp/calclyo.com.conf"

ssh "$REMOTE_HOST" "cd '$REMOTE_APP_DIR' && ([ -f .env ] || cp .env.example .env) && sed -i \"s#^APP_NAME=.*#APP_NAME='Calclyo'#\" .env && sed -i \"s#^APP_ENV=.*#APP_ENV=production#\" .env && sed -i \"s#^APP_DEBUG=.*#APP_DEBUG=false#\" .env && sed -i \"s#^APP_URL=.*#APP_URL=https://calclyo.com#\" .env && sed -i \"s#^APP_KEY=.*#APP_KEY=$APP_KEY#\" .env && grep -q '^APP_LOCALE=' .env || printf '\nAPP_LOCALE=ar\n' >> .env && grep -q '^APP_FALLBACK_LOCALE=' .env || printf 'APP_FALLBACK_LOCALE=ar\n' >> .env && grep -q '^DB_CONNECTION=' .env || printf 'DB_CONNECTION=mysql\n' >> .env && grep -q '^DB_HOST=' .env || printf 'DB_HOST=127.0.0.1\n' >> .env && grep -q '^DB_PORT=' .env || printf 'DB_PORT=3306\n' >> .env && grep -q '^DB_DATABASE=' .env || printf 'DB_DATABASE=\n' >> .env && grep -q '^DB_USERNAME=' .env || printf 'DB_USERNAME=\n' >> .env && grep -q '^DB_PASSWORD=' .env || printf 'DB_PASSWORD=\n' >> .env && sed -i \"s#^DB_CONNECTION=.*#DB_CONNECTION=mysql#\" .env && sed -i \"s#^DB_HOST=.*#DB_HOST=127.0.0.1#\" .env && sed -i \"s#^DB_PORT=.*#DB_PORT=3306#\" .env && sed -i \"s#^DB_DATABASE=.*#DB_DATABASE=$DB_NAME#\" .env && sed -i \"s#^DB_USERNAME=.*#DB_USERNAME=$DB_USER#\" .env && sed -i \"s#^DB_PASSWORD=.*#DB_PASSWORD=$DB_PASSWORD#\" .env && composer install --no-interaction --prefer-dist --optimize-autoloader && npm install && npm run build && php artisan migrate --force && php artisan db:seed --class=ToolsPlatformSeeder --force && (php artisan storage:link || true) && php artisan cache:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache"

ssh "$REMOTE_HOST" "sudo mv /tmp/calclyo.com.conf /etc/nginx/sites-available/calclyo.com.conf && sudo ln -sfn /etc/nginx/sites-available/calclyo.com.conf /etc/nginx/sites-enabled/calclyo.com.conf && sudo nginx -t && sudo systemctl reload nginx && sudo chown -R www-data:www-data '$REMOTE_APP_DIR/storage' '$REMOTE_APP_DIR/bootstrap/cache'"

echo "Deployment finished for calclyo.com"
