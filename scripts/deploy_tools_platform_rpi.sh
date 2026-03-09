#!/usr/bin/env bash

set -euo pipefail

REMOTE_HOST="${REMOTE_HOST:-jarvis@jarvis.local}"
REMOTE_APP_DIR="${REMOTE_APP_DIR:-/var/www/tools.bakerysoft.net/platform}"
LOCAL_APP_DIR="${LOCAL_APP_DIR:-tools-platform/}"
LOCAL_NGINX_CONF="${LOCAL_NGINX_CONF:-deploy/nginx/tools-platform.bakerysoft.net.conf}"
DB_NAME="${DB_NAME:-bakerysoft_tools}"
DB_USER="${DB_USER:-bakerysoft_tools}"
DB_PASSWORD="${DB_PASSWORD:-change_me_now}"
APP_KEY="${APP_KEY:-}"

if [[ -z "$APP_KEY" ]]; then
  APP_KEY="$(php -r "require '$LOCAL_APP_DIR/vendor/autoload.php'; echo 'base64:'.base64_encode(random_bytes(32));")"
fi

ssh "$REMOTE_HOST" "sudo mkdir -p '$REMOTE_APP_DIR' && sudo chown -R \$(id -un):\$(id -gn) '$REMOTE_APP_DIR'"
rsync -av --delete --exclude '.env' --exclude 'vendor' --exclude 'node_modules' --exclude 'storage/logs/*' "$LOCAL_APP_DIR" "$REMOTE_HOST:$REMOTE_APP_DIR/"
scp "$LOCAL_NGINX_CONF" "$REMOTE_HOST:/tmp/tools-platform.bakerysoft.net.conf"

ssh "$REMOTE_HOST" "cd '$REMOTE_APP_DIR' && ([ -f .env ] || cp .env.example .env) && sed -i \"s#^APP_NAME=.*#APP_NAME='BakerySoft Tools'#\" .env && sed -i \"s#^APP_ENV=.*#APP_ENV=production#\" .env && sed -i \"s#^APP_DEBUG=.*#APP_DEBUG=false#\" .env && sed -i \"s#^APP_URL=.*#APP_URL=https://tools.bakerysoft.net#\" .env && sed -i \"s#^APP_KEY=.*#APP_KEY=$APP_KEY#\" .env && printf '\nAPP_LOCALE=ar\nAPP_FALLBACK_LOCALE=ar\nDB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=$DB_NAME\nDB_USERNAME=$DB_USER\nDB_PASSWORD=$DB_PASSWORD\n' >> .env && composer install --no-interaction --prefer-dist --optimize-autoloader && npm install && npm run build && php artisan migrate --force && php artisan db:seed --class=ToolsPlatformSeeder --force && (php artisan storage:link || true) && php artisan cache:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache"

ssh "$REMOTE_HOST" "sudo mv /tmp/tools-platform.bakerysoft.net.conf /etc/nginx/sites-available/tools.bakerysoft.net.conf && sudo ln -sfn /etc/nginx/sites-available/tools.bakerysoft.net.conf /etc/nginx/sites-enabled/tools.bakerysoft.net.conf && sudo nginx -t && sudo systemctl reload nginx && sudo chown -R www-data:www-data '$REMOTE_APP_DIR/storage' '$REMOTE_APP_DIR/bootstrap/cache'"

echo "Deployment finished for tools.bakerysoft.net"
