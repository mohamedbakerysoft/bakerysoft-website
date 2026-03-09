#!/usr/bin/env bash

set -euo pipefail

REMOTE_HOST="${REMOTE_HOST:-jarvis@jarvis.local}"
REPO_URL="${REPO_URL:-https://github.com/mohamedbakerysoft/bakerysoft-website.git}"
REPO_DIR="${REPO_DIR:-/var/www/tools.bakerysoft.net/source}"

ssh "$REMOTE_HOST" "sudo mkdir -p '$(dirname "$REPO_DIR")' && sudo chown -R \$(id -un):\$(id -gn) '$(dirname "$REPO_DIR")'"
ssh "$REMOTE_HOST" "[ -d '$REPO_DIR/.git' ] || git clone '$REPO_URL' '$REPO_DIR'"
ssh "$REMOTE_HOST" "cd '$REPO_DIR' && chmod +x scripts/server_deploy_tools_platform_from_repo.sh"

echo "GitHub source checkout is ready at $REPO_DIR"
