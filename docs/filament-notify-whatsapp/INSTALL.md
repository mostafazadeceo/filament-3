# INSTALL — filament-notify-whatsapp

## پیش‌نیازها
- PHP 8.4+
- Laravel 12+ / Filament v4 (طبق استاندارد پروژه)
- فعال بودن IAM Suite و tenant resolution

## نصب
1) پکیج در monorepo حاضر است (path repository در composer).
2) Composer autoload را به‌روزرسانی کنید:
   - `composer dump-autoload`
3) مهاجرت‌ها:
   - `php artisan migrate --force`

## فعال‌سازی در Filament
- اگر پکیج Plugin دارد، ثبت در پنل‌ها:
  - Admin: Plugin ندارد
  - Tenant: Plugin ندارد

## IAM و مجوزها
- همگام‌سازی capabilityها:
  - `php artisan filamat-iam:sync --guard=web`
- اطمینان از scopeهای API طبق `filamat-iam.scope:<scope>`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-notify-whatsapp/config/filament-notify-whatsapp.php
- انتشار تنظیمات (در صورت نیاز):
  - `php artisan vendor:publish --tag=filament-notify-whatsapp-config`
## تست/اعتبارسنجی
- سناریوی عمیق: `php scripts/deep_scenario_runner.php`
