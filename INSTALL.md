# Installation Guide

This is a production-oriented quick install guide for the Haida Filament Platform.

## 1) Environment

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL/PostgreSQL

## 2) Setup

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --force
npm install
npm run build
```

## 3) WebPush VAPID Keys (optional)

```bash
php -r "require 'vendor/autoload.php'; print_r(\Minishlink\WebPush\VAPID::createVapidKeys());"
```

Then go to `Settings -> Channel Settings -> WebPush` and add the keys.

## 4) Admin Panel

Log in and configure:
- Email SMTP
- SMS IPPanel
- Telegram / WhatsApp / Bale
- WebPush

## 5) One-command install

```bash
bash scripts/install.sh
```
