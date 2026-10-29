#!/usr/bin/env bash
# Exit on error
set -o errexit

echo "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

echo "Installing NPM dependencies & building assets..."
npm install
npm run build

echo "Optimizing application..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan storage:link || true
