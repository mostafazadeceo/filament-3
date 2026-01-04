# INSTALL — افزونه 3CX

## نصب بسته
1) افزودن بسته به وابستگی‌ها:
```
composer require haida/filament-threecx
```
2) اجرای مهاجرت‌ها:
```
php artisan migrate --force
```

## فعال‌سازی در پنل‌ها
در هر پنل مورد نیاز (Tenant/Admin) پلاگین را اضافه کنید:
```
use Haida\FilamentThreeCx\Filament\FilamentThreeCxPlugin;

// ...
$plugins[] = FilamentThreeCxPlugin::make();
```

## تنظیمات اولیه
- انتشار تنظیمات (اختیاری):
```
php artisan vendor:publish --tag=filament-threecx-config
```
- فایل تنظیمات: `config/filament-threecx.php`
- فعال/غیرفعال کردن قابلیت‌ها و نرخ محدودسازی در همین فایل انجام می‌شود.

## مجوزها و نقش‌ها
برای نقش‌های موردنظر مجوزها را تخصیص دهید:
- `threecx.view`
- `threecx.manage`
- `threecx.sync`
- `threecx.api_explorer`
- `threecx.crm_connector`

## اتصال 3CX
1) در پنل، یک «اتصال 3CX» بسازید.
2) `base_url`, `client_id`, `client_secret` را وارد کنید.
3) قابلیت‌های مورد نیاز را فعال کنید (XAPI/Call Control/CRM Connector).
4) با دکمه «تست اتصال» وضعیت را بررسی کنید.

## CRM Connector
دو حالت احراز هویت وجود دارد:
1) **Connector Key (پیش‌فرض)**:
   - برای اتصال، `crm_connector_key` تنظیم کنید.
   - 3CX باید هدر `X-ThreeCX-Connector-Key` را ارسال کند.
2) **API Key**:
   - مقدار `auth_mode` را روی `api_key` قرار دهید.
   - 3CX باید هدر `X-Api-Key` را ارسال کند.

## محدودیت‌های امنیتی
- هیچ قابلیت ضبط/پخش/استریم صدا یا شنود پیاده‌سازی نشده است.
- مسیرهای مرتبط با این قابلیت‌ها در API Explorer مسدود هستند.
