# INSTALL — Meetings AI

## پیش‌نیازها
- Laravel 12+
- PHP 8.4+
- Filament v4
- IAM Suite فعال (tenancy + permissions)

## نصب پکیج‌ها (لوکال)
1) اطمینان از وجود پکیج‌ها:
   - `packages/filament-ai-core`
   - `packages/filament-meetings`
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
$plugins[] = \Haida\FilamentMeetings\FilamentMeetingsPlugin::make();
```

## مجوزها
- مجوزهای Meetings از طریق CapabilityRegistry ثبت می‌شوند.

## تنظیمات
- پیکربندی Provider و سیاست‌ها در `config/filament-ai-core.php`.
- تنظیمات Meetings در `config/filament-meetings.php`.
  - صف‌گذاری AI: `filament-meetings.ai.queue.enabled`
  - تنظیمات صف: `filament-meetings.ai.queue.connection` و `filament-meetings.ai.queue.queue`

## کران‌جاب‌ها
- ارسال جمع‌بندی هفتگی اقدام‌های جلسات:

```bash
php artisan meetings:send-weekly-digest --tenant=<id>
```
