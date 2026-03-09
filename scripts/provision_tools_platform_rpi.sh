#!/usr/bin/env bash

set -euo pipefail

REMOTE_HOST="${REMOTE_HOST:-jarvis@jarvis.local}"
APP_DIR="${APP_DIR:-/var/www/calclyo.com/platform}"
DB_NAME="${DB_NAME:-calclyo_tools}"
DB_USER="${DB_USER:-calclyo_tools}"
DB_PASSWORD="${DB_PASSWORD:-change_me_now}"

ssh "$REMOTE_HOST" "sudo apt-get update"
ssh "$REMOTE_HOST" "sudo apt-get install -y ca-certificates curl lsb-release apt-transport-https gnupg2 unzip git nginx mariadb-server"
ssh "$REMOTE_HOST" "curl -fsSL https://packages.sury.org/php/apt.gpg | sudo gpg --dearmor -o /usr/share/keyrings/sury-php.gpg"
ssh "$REMOTE_HOST" "echo 'deb [signed-by=/usr/share/keyrings/sury-php.gpg] https://packages.sury.org/php/ bullseye main' | sudo tee /etc/apt/sources.list.d/sury-php.list >/dev/null"
ssh "$REMOTE_HOST" "sudo apt-get update"
ssh "$REMOTE_HOST" "sudo apt-get install -y php8.3 php8.3-cli php8.3-fpm php8.3-mysql php8.3-xml php8.3-mbstring php8.3-curl php8.3-zip php8.3-intl php8.3-bcmath php8.3-gd"
ssh "$REMOTE_HOST" "EXPECTED_CHECKSUM=\$(php -r \"copy('https://composer.github.io/installer.sig', 'php://stdout');\") && php -r \"copy('https://getcomposer.org/installer', 'composer-setup.php');\" && ACTUAL_CHECKSUM=\$(php -r \"echo hash_file('sha384', 'composer-setup.php');\") && [ \"\$EXPECTED_CHECKSUM\" = \"\$ACTUAL_CHECKSUM\" ] && sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer && rm composer-setup.php"
ssh "$REMOTE_HOST" "sudo mkdir -p '$APP_DIR' && sudo chown -R \$(id -un):\$(id -gn) '$APP_DIR'"
ssh "$REMOTE_HOST" "sudo mysql -e \"CREATE DATABASE IF NOT EXISTS \\\`$DB_NAME\\\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASSWORD'; GRANT ALL PRIVILEGES ON \\\`$DB_NAME\\\`.* TO '$DB_USER'@'localhost'; FLUSH PRIVILEGES;\""
ssh "$REMOTE_HOST" "sudo systemctl enable --now php8.3-fpm nginx mariadb"

echo "Provisioning finished for $REMOTE_HOST"
