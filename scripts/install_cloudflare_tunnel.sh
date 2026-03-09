#!/usr/bin/env bash

set -euo pipefail

if [[ $# -ne 1 ]]; then
  echo "Usage: $0 <cloudflare_tunnel_token>"
  exit 1
fi

TUNNEL_TOKEN="$1"
REMOTE_HOST="${REMOTE_HOST:-jarvis@jarvis.local}"

ssh "$REMOTE_HOST" "sudo cloudflared service uninstall >/dev/null 2>&1 || true"
ssh "$REMOTE_HOST" "sudo cloudflared service install '$TUNNEL_TOKEN'"
ssh "$REMOTE_HOST" "sudo systemctl enable --now cloudflared"
ssh "$REMOTE_HOST" "sudo systemctl is-active cloudflared && sudo systemctl --no-pager --full status cloudflared | sed -n '1,20p'"
