# نصب و راه‌اندازی Mailtrap

## پیش‌نیازها
- PHP 8.4+
- Laravel 12+
- Filament v4

## نصب پکیج‌ها
```bash
composer require haida/mailtrap-core haida/filament-mailtrap haida/filament-notify-mailtrap
```

## مهاجرت دیتابیس
```bash
php artisan migrate --force
```

## تنظیمات محیط
در `.env`:
```
MAILTRAP_BASE_URL=https://mailtrap.io/api
MAILTRAP_SEND_BASE_URL=https://send.api.mailtrap.io/api
MAILTRAP_SEND_API_TOKEN=your-send-token
MAILTRAP_FROM_ADDRESS=hello@example.com
MAILTRAP_FROM_NAME=Mailtrap
```

## فعال‌سازی UI
پلاگین `MailtrapPlugin` در پنل‌های Admin و Tenant ثبت شده است.

## فعال‌سازی کانال اعلان
در پنل Filament:
- مسیر «اطلاع‌رسانی → تنظیمات کانال‌ها»
- کانال Mailtrap را با توکن Send API فعال کنید.

## همگام‌سازی اولیه
- اتصال Mailtrap بسازید.
- «آزمون اتصال» را اجرا کنید.
- Inboxها و دامنه‌ها را همگام کنید.

