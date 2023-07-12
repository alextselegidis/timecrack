#!/bin/bash

composer install
npm install
find . -name ".DS_Store" -delete
rm public/hot
npm run build
zip -r timecrack-0.0.0.zip . \
    -x .git/**\* \
    -x .idea/**\* \
    -x tests/**\* \
    -x .editorconfig \
    -x .gitattributes \
    -x .phpstorm.meta.php \
    -x .prettierignore \
    -x package-lock.json \
    -x phpunit.xml \
    -x _ide_helper.php \
    -x vite.config.js \
    -x \*.zip
