# INSTALL — Workhub

## پیش‌نیازها
- Laravel 12 / Filament v4
- IAM Suite فعال (tenancy + permissions)

## نصب پکیج (لوکال)
1) اطمینان از وجود پکیج در مسیر `packages/filament-workhub`.
2) افزودن پکیج به `composer.json` (path repository + require).
3) اجرای دستورهای استاندارد نصب:

```bash
composer install
php artisan migrate
```

## فعال‌سازی در پنل‌ها
در `App\Providers\Filament\AdminPanelProvider` و `TenantPanelProvider`:

```php
$plugins[] = \Haida\FilamentWorkhub\FilamentWorkhubPlugin::make();
```

## مجوزها
- مجوزها از طریق IAM Suite و CapabilityRegistry ثبت می‌شوند.
- نقش‌ها را از پنل مدیریت دسترسی تنظیم کنید.

## نکات تننت
- همه‌ی داده‌ها با `tenant_id` اسکوپ می‌شوند.
- برای API از هدر `X-Tenant-ID` استفاده کنید.

## اتوماسیون زمان‌بندی‌شده
برای اجرای قوانین زمان‌بندی‌شده:

```bash
php artisan workhub:automation:run
```

برای اجرا در کرون، دستور بالا را در Scheduler پروژه ثبت کنید.
