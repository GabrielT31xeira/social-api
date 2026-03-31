#!/usr/bin/env bash

set -e

export HOME=/tmp

cd /var/www/html

git config --global --add safe.directory /var/www/html || true

if [ ! -f vendor/autoload.php ]; then
  composer install
fi

if [ ! -f .env ]; then
  cp .env.example .env
fi

if ! grep -Eq '^APP_KEY=.+$' .env; then
  php artisan key:generate --force
fi

until php artisan migrate --force; do
  echo "Waiting for database..."
  sleep 2
done

exec php-fpm
