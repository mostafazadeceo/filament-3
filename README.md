# Haida Filament Platform

A Laravel 12 + Filament v4 platform with a modular notification system (email, SMS, Telegram, WhatsApp, Bale, WebPush) and admin tooling.

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+ / npm
- MySQL or PostgreSQL

## Quick Install

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --force
npm install
npm run build
```

If this is production, set the correct `APP_ENV`, `APP_KEY`, and database credentials in `.env` before migrating.

## Filament Notify (Core + Add-ons)

The notification system is already bundled in `packages/` and registered in the panel provider.

- Core: `packages/filament-notify-core`
- SMS (IPPanel): `packages/filament-notify-sms-ippanel`
- Telegram: `packages/filament-notify-telegram`
- WhatsApp: `packages/filament-notify-whatsapp`
- Bale: `packages/filament-notify-bale`
- WebPush: `packages/filament-notify-webpush`

### WebPush VAPID Keys

Generate VAPID keys and add them in `Settings -> Channel Settings -> WebPush`:

```bash
php -r "require 'vendor/autoload.php'; print_r(\Minishlink\WebPush\VAPID::createVapidKeys());"
```

## Easy Install Script

```bash
bash scripts/install.sh
```

## License

This project is proprietary and licensed for commercial use only.

- Annual license: USD 1200
- Owner: Mohammad Mostafazadeh (09120120997)

See `LICENSE` for details.
