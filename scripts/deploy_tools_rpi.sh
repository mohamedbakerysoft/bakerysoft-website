#!/usr/bin/env bash

set -euo pipefail

REMOTE_HOST="${REMOTE_HOST:-jarvis@jarvis.local}"
REMOTE_APP_DIR="${REMOTE_APP_DIR:-/var/www/tools.bakerysoft.net}"
LOCAL_SITE_DIR="${LOCAL_SITE_DIR:-tools-site/}"
LOCAL_NGINX_CONF="${LOCAL_NGINX_CONF:-deploy/nginx/tools.bakerysoft.net.conf}"

if [[ ! -f "${LOCAL_SITE_DIR%/}/index.html" ]]; then
  echo "Missing ${LOCAL_SITE_DIR%/}/index.html."
  exit 1
fi

ssh "$REMOTE_HOST" "sudo mkdir -p '$REMOTE_APP_DIR'"
ssh "$REMOTE_HOST" "sudo chown -R \$(id -un):\$(id -gn) '$REMOTE_APP_DIR'"
rsync -av --delete "$LOCAL_SITE_DIR" "$REMOTE_HOST:$REMOTE_APP_DIR/"
scp "$LOCAL_NGINX_CONF" "$REMOTE_HOST:/tmp/tools.bakerysoft.net.conf"
ssh "$REMOTE_HOST" "sudo chown -R www-data:www-data '$REMOTE_APP_DIR' && sudo mv /tmp/tools.bakerysoft.net.conf /etc/nginx/sites-available/tools.bakerysoft.net.conf && sudo ln -sfn /etc/nginx/sites-available/tools.bakerysoft.net.conf /etc/nginx/sites-enabled/tools.bakerysoft.net.conf && sudo nginx -t && sudo systemctl reload nginx"

echo "Deployment finished. Test locally with: http://tools.bakerysoft.net"
