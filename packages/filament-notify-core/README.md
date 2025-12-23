# Filament Notify Core

هسته اطلاع‌رسانی برای Filament v4 شامل قوانین، قالب‌ها، تریگرها و لاگ ارسال.

## نصب

```bash
composer require haida/filament-notify-core
```

سپس در پنل Filament:

```php
->plugin(\Haida\FilamentNotify\Core\FilamentNotifyPlugin::make())
```

## امکانات

- کشف خودکار تریگرها از اکشن‌های Filament
- قالب‌ها و قوانین اطلاع‌رسانی
- لاگ ارسال
- پشتیبانی از ایمیل به‌صورت پیش‌فرض

## تنظیمات

فایل `config/filament-notify.php` را بر اساس نیاز تنظیم کنید.
