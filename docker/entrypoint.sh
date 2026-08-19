#!/bin/sh
set -e

cd /var/www/html

# APP_KEY 需跨容器重啟持久化，因此存放於掛載卷內，而非依賴 .env 檔案。
KEY_FILE=storage/app/.app_key
if [ -z "$APP_KEY" ]; then
    if [ -f "$KEY_FILE" ]; then
        APP_KEY=$(cat "$KEY_FILE")
    else
        APP_KEY="base64:$(openssl rand -base64 32)"
        echo -n "$APP_KEY" > "$KEY_FILE"
    fi
    export APP_KEY
fi

if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
fi

chown -R www-data:www-data storage bootstrap/cache database
chmod -R 775 storage bootstrap/cache

php artisan migrate --force
php artisan db:seed --class="Database\\Seeders\\AdminUserSeeder" --force
php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
