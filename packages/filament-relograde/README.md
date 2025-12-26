# افزونه رلوگرید برای فیلامنت

افزونه آماده تولید برای اتصال به ای‌پی‌آی رلوگرید (نسخه 1.02). شامل کاتالوگ، سفارش‌ها، موجودی‌ها، وب‌هوک، لاگ‌ها، تنظیمات، همگام‌سازی، کش، پایش، هشدارها، خروجی‌ها و گزارش‌های ممیزی.

## پیش‌نیازها

- PHP نسخه 8.2 به بالا
- Laravel نسخه 10/11/12
- Filament نسخه 3 یا 4
- صف برای اجرای جاب‌های همگام‌سازی
- زمان‌بند برای پایش و همگام‌سازی

## نصب

```bash
composer require haida/filament-relograde
php artisan relograde:install --migrate
```

ثبت افزونه در پنل (در این پروژه از قبل انجام شده است):

```php
use Haida\FilamentRelograde\RelogradePlugin;

$panel->plugins([
    RelogradePlugin::make(),
]);
```

## پیکربندی

انتشار فایل پیکربندی (در صورت نیاز):

```bash
php artisan vendor:publish --tag=filament-relograde-config
```

گزینه‌های مهم در `config/relograde.php`:

- `base_url`, `api_version`
- `cache` (فعال‌سازی و TTL)
- `rate_limit` (۶۰ درخواست در دقیقه)
- `webhooks.allowed_ips`
- `schedule` و `polling`
- `low_balance_thresholds`
- `encrypt_voucher_codes`
- `permissions_enabled` (در صورت false، همه کاربران مجاز هستند)
- `permissions`

## وب‌هوک

مسیر دریافت:

```
POST /relograde/webhook/order-finished
```

اعتبارسنجی‌ها:
- Content-Type باید `application/json` باشد
- `event == ORDER_FINISHED`
- آی‌پی فرستنده در لیست مجاز (پیش‌فرض `18.195.134.217`)
- هدر اختیاری `X-Relograde-Secret`

پیشنهاد: برای هر اتصال، رمز وب‌هوک تنظیم کنید و آی‌پی‌ها را محدود نمایید.

## نمای کلی رابط مدیریت

گروه ناوبری: **رلوگرید**

- داشبورد (موجودی‌ها، وضعیت سفارش‌ها، موجودی کم، موجودی محصولات)
- اتصال‌ها (تنظیمات + پیکربندی وب‌هوک)
- کاتالوگ: برندها، محصولات
- موجودی‌ها
- سفارش‌ها (ایجاد + تکمیل + خروجی ووچر)
- رویدادهای وب‌هوک
- لاگ‌های ای‌پی‌آی
- هشدارها (پایش موجودی کم)
- گزارش‌های ممیزی (اقدامات کاربران)

## همگام‌سازی و زمان‌بندی

جاب‌ها:
- `SyncBrandsJob`
- `SyncProductsJob`
- `SyncAccountsJob`
- `PollPendingOrdersJob`
- `ProcessWebhookEventJob`
- `CheckLowBalanceAlertsJob`

زمان‌بندی به صورت پیش‌فرض فعال است. مطمئن شوید `php artisan schedule:run` و صف اجرا می‌شوند.

## خروجی‌ها

- خروجی سی‌اس‌وی همیشه در دسترس است.
- خروجی پی‌دی‌اف نیازمند نصب بسته `barryvdh/laravel-dompdf` است.

## مجوزها

کلیدهای مجوز سازگار با Filament Shield (در صورت نیاز در تنظیمات تغییر دهید):

- `relograde.view`
- `relograde.sync`
- `relograde.orders.create`
- `relograde.orders.fulfill`
- `relograde.vouchers.reveal`
- `relograde.logs.view`
- `relograde.settings.manage`

## تست‌ها

تست‌های پکیج (Testbench):

```bash
cd packages/filament-relograde
vendor/bin/phpunit
```

## اسکرین‌شات‌ها

اینجا اضافه کنید:

- `docs/screenshots/dashboard.png`
- `docs/screenshots/orders.png`
- `docs/screenshots/catalog.png`

## یادداشت‌ها

- کلیدهای ای‌پی‌آی و رمزهای وب‌هوک به‌صورت رمزنگاری‌شده ذخیره می‌شوند.
- هدرهای Authorization از لاگ‌های ای‌پی‌آی حذف می‌شوند.
- کش برای اندپوینت‌های کاتالوگ فعال است.
- هشدارهای موجودی کم در `relograde_alerts` ذخیره و در داشبورد نمایش داده می‌شوند.
- گزارش‌های ممیزی اقدامات مدیران در `relograde_audit_logs` ثبت می‌شوند.
