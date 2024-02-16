#!/bin/bash

# Dependencies

composer install

npm install

# Empty Storage

find storage/app -type f ! -name '.gitignore' -exec rm -f {} \;

# Clear Cache

php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan clear-compiled

# Remove Various

rm public/hot

find . -name ".DS_Store" -delete

# Build Assets

npm run build

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
    -x 'build.sh' \
    -x 'docker-compose.yml' \
    -x 'postcss.config.js' \
    -x 'tailwind.config.js' \
    -x 'vite.config.js' \
    -x '\*.zip'
