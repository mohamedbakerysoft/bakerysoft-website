#!/usr/bin/env bash

set -euo pipefail

REMOTE_HOST="${REMOTE_HOST:-jarvis@jarvis.local}"
REMOTE_APP_DIR="${REMOTE_APP_DIR:-/var/www/bakerysoft.net}"
LOCAL_DIST_DIR="${LOCAL_DIST_DIR:-dist/}"
LOCAL_NGINX_CONF="${LOCAL_NGINX_CONF:-deploy/nginx/bakerysoft.net.conf}"

if [[ ! -d "$LOCAL_DIST_DIR" ]]; then
  echo "Missing $LOCAL_DIST_DIR. Run npm install && npm run build first."
  exit 1
fi

if [[ ! -f "$LOCAL_NGINX_CONF" ]]; then
  echo "Missing $LOCAL_NGINX_CONF."
  exit 1
fi

ssh "$REMOTE_HOST" "sudo mkdir -p '$REMOTE_APP_DIR'"
ssh "$REMOTE_HOST" "sudo chown -R \$(id -un):\$(id -gn) '$REMOTE_APP_DIR'"
rsync -av --delete "$LOCAL_DIST_DIR" "$REMOTE_HOST:$REMOTE_APP_DIR/"
scp "$LOCAL_NGINX_CONF" "$REMOTE_HOST:/tmp/bakerysoft.net.conf"
ssh "$REMOTE_HOST" "sudo chown -R www-data:www-data '$REMOTE_APP_DIR' && sudo mv /tmp/bakerysoft.net.conf /etc/nginx/sites-available/bakerysoft.net.conf && sudo ln -sfn /etc/nginx/sites-available/bakerysoft.net.conf /etc/nginx/sites-enabled/bakerysoft.net.conf && sudo rm -f /etc/nginx/sites-enabled/default && sudo nginx -t && sudo systemctl reload nginx"

echo "Deployment finished. Test locally with: http://$(echo "$REMOTE_HOST" | sed 's/.*@//')/"
