#!/bin/sh
set -e

# Run database migrations
php artisan migrate --force || true

# Optimize Laravel for production
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

exec apache2-foreground
