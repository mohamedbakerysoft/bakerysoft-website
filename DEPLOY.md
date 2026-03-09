# BakerySoft deployment on Raspberry Pi

This project can be deployed as a static site behind `nginx`.

## Important note

The page includes a form posting to `./contactUs/index.php`.
If you only serve the built output with `nginx`, that PHP endpoint will not run.
You need one of these options if the contact form must work:

- add `php-fpm` on the Raspberry Pi and configure `nginx` for PHP
- replace the form target with an API endpoint or third-party form service

## 1. Build locally

```bash
npm install
npm run build
```

## 2. Deploy to the Raspberry Pi

```bash
chmod +x scripts/deploy_rpi.sh
./scripts/deploy_rpi.sh
```

Defaults:

- SSH host: `jarvis@jarvis.local`
- app path on Pi: `/var/www/bakerysoft.net`
- nginx config: `deploy/nginx/bakerysoft.net.conf`

Override example:

```bash
REMOTE_HOST=jarvis@192.168.1.2 REMOTE_APP_DIR=/var/www/bakerysoft.net ./scripts/deploy_rpi.sh
```

## 3. Cloudflare DNS

Point the domain to your home public IP:

- `A` record for `bakerysoft.net` -> your router public IPv4
- `CNAME` record for `www` -> `bakerysoft.net`

Recommended Cloudflare settings:

- proxy status: orange cloud enabled
- SSL/TLS mode: `Full`
- if port forwarding is configured on your router, forward `80` and `443` to the Raspberry Pi local IP

## 4. HTTPS on the Pi

For end-to-end HTTPS, install a certificate on the Pi, for example with `certbot`, then add a TLS server block in `nginx`.
