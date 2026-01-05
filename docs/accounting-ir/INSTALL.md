# نصب Accounting IR

## پیش‌نیازها
- PHP 8.4+
- Laravel 12+
- Filament v4
- فعال بودن IAM Suite

## نصب پکیج
```bash
composer require vendor/filament-accounting-ir
php artisan filament-accounting:install --migrate
```

## فعال‌سازی در پنل‌ها
- پلاگین در هر دو پنل Admin و Tenant ثبت شده است.
- در صورت سفارشی‌سازی پنل‌ها، مطمئن شوید `FilamentAccountingIrPlugin::make()` اضافه شده باشد.

## راه‌اندازی اولیه
- ایجاد شرکت، شعبه و سال مالی.
- تعریف پلن کدینگ و کدینگ حساب‌ها.
- تعریف دوره مالی و سیاست قفل دوره.

## تنظیمات شرکت
- از مسیر «تنظیمات > تنظیمات شرکت» حساب‌های پیش‌فرض فروش/خرید را تعیین کنید.
- این تنظیمات برای ثبت اتومات اسناد و کنترل موجودی منفی استفاده می‌شود.

## صف‌ها و یکپارچه‌سازی
- برای ارسال سامانه مؤدیان و وبهوک‌ها، صف را فعال کنید:
```bash
php artisan queue:work
```
- اجرای یکپارچه‌سازی‌ها:
```bash
php artisan accounting-ir:run-integrations
```
