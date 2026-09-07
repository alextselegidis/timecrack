#!/bin/bash

# Run From The Host

CONTAINER=timecrack-php-fpm-1

if [ "$(docker inspect -f '{{.State.Running}}' "$CONTAINER" 2>/dev/null)" != "true" ]; then
    echo "The $CONTAINER container is not running, start it with: docker compose up -d"
    exit 1
fi

php() {
    docker exec "$CONTAINER" sh -c "cd /var/www/html && php $*"
}

composer() {
    docker exec "$CONTAINER" sh -c "cd /var/www/html && composer $*"
}

# Dependencies

composer install

# Empty Storage

find storage/app -type f ! -name '.gitignore' -exec rm -f {} \;
find storage/logs -type f ! -name '.gitignore' -exec rm -f {} \;

# Clear Cache

php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan clear-compiled

# Remove Various

rm -f timecrack-0.0.0.zip

rm -f public/hot

find . -name ".DS_Store" -delete

# Zip Files

zip -r timecrack-0.0.0.zip . \
    -x '.git/*' \
    -x '.idea/*' \
    -x '.run/*' \
    -x 'docker/*' \
    -x 'node_modules/*' \
    -x 'tests/*' \
    -x '.editorconfig' \
    -x '.gitattributes' \
    -x '.gitignore' \
    -x '.prettierignore' \
    -x '.package-lock.json' \
    -x '.env' \
    -x 'build.sh' \
    -x 'docker-compose.yml' \
    -x 'postcss.config.js' \
    -x 'vite.config.js' \
    -x 'SPECS.md' \
    -x '*.zip'
