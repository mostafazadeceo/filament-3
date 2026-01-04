# INSTALL — Workhub AI

## پیش‌نیازها
- Laravel 11.28+
- PHP 8.2+
- Filament v4
- IAM Suite فعال (tenancy + permissions)

## نصب پکیج‌ها (لوکال)
1) اطمینان از وجود پکیج‌ها:
   - `packages/filament-ai-core`
   - `packages/filament-workhub`
2) افزودن path repository و require به `composer.json` (root).
3) اجرای نصب:

```bash
composer install
php artisan migrate
```

## فعال‌سازی در پنل‌ها
در `App\Providers\Filament\AdminPanelProvider` و `TenantPanelProvider`:

```php
$plugins[] = \Haida\FilamentAiCore\FilamentAiCorePlugin::make();
$plugins[] = \Haida\FilamentWorkhub\FilamentWorkhubPlugin::make();
```

## مجوزها
- مجوزهای AI از طریق CapabilityRegistry ثبت می‌شوند.
- نقش‌ها/سطوح دسترسی را از پنل IAM تنظیم کنید.

## نکات تننت
- همه‌ی داده‌ها با `tenant_id` اسکوپ می‌شوند.
- API از `ApiKeyAuth` و `ResolveTenant` استفاده می‌کند.

## تنظیمات
- پیکربندی Provider و سیاست‌ها در `config/filament-ai-core.php`.
- تنظیمات Workhub AI در `config/filament-workhub.php` (TTL، نرخ‌ها، گزارش‌ها).

## گزارش‌های دوره‌ای
- اجرای دستی:

```bash
php artisan workhub:ai:audit --days=30
```

- برای اجرای شبانه/هفتگی، این دستور را در scheduler قرار دهید.
