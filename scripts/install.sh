#!/usr/bin/env bash
set -euo pipefail

if [ ! -f .env ]; then
  cp .env.example .env
fi

if [ -f composer.json ]; then
  composer install
fi

php artisan key:generate
php artisan migrate --force

if [ -f package.json ]; then
  npm install
  npm run build
fi

echo "Install complete. Configure mail/SMS/WebPush in the admin panel."
