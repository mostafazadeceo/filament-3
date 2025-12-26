# راهنمای کامل Filament API Docs Builder (فارسی)

این راهنما برای بسته‌ی `zpmlabs/filament-api-docs-builder` است که در این پروژه نصب و با چند-اجاره‌گی Filamat IAM Suite سازگار شده است.

## نصب سریع

> در این پروژه نصب انجام شده است؛ این بخش برای محیط‌های جدید است.

```bash
composer require zpmlabs/filament-api-docs-builder
php artisan filament-api-docs-builder:install
php artisan migrate
php artisan optimize:clear
```

## فعال‌سازی در پنل‌های Filament

در هر PanelProvider افزونه را اضافه کنید (در این پروژه برای `admin` و `tenant` انجام شده است):

```php
use ZPMLabs\FilamentApiDocsBuilder\FilamentApiDocsBuilderPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            FilamentApiDocsBuilderPlugin::make(),
        ]);
}
```

## تنظیمات کلیدی

فایل تنظیمات:
- `config/filament-api-docs-builder.php`

مهم‌ترین گزینه‌ها:
- `model`: در این پروژه روی `Filamat\IamSuite\Models\ApiDoc` تنظیم شده تا Tenant Scope اعمال شود.
- `tenant`: روی `Filamat\IamSuite\Models\Tenant` تنظیم شده است.
- `predefined_params`: هدرهای پیش‌فرض مثل `Authorization`, `Accept`, `Content-Type`.

نمونه‌ی تنظیم هدرها:
```php
'predefined_params' => [
  [
    'location' => 'header',
    'type' => 'string',
    'name' => 'Authorization',
    'value' => 'Bearer $TOKEN',
    'required' => true,
  ],
],
```

## ساخت اولین سند API

1) وارد پنل شوید و به بخش **API Docs** بروید.
2) روی **ایجاد** کلیک کنید.
3) عنوان و توضیحات کلی سند را وارد کنید.
4) برای هر Endpoint:
   - متد (GET/POST/PUT/DELETE)
   - مسیر (Path)
   - توضیحات
   - پارامترها (Header/Query/Body)
   - نمونه درخواست و پاسخ
5) ذخیره کنید و صفحه‌ی نمایش (View) را ببینید.

## پارامترها، پاسخ‌ها و مثال‌ها

- هر پارامتر می‌تواند نوع، توضیح، اجباری بودن و مقدار پیش‌فرض داشته باشد.
- برای پاسخ‌ها، کد وضعیت و نمونه‌ی JSON/متن را ثبت کنید.
- اگر از `Content-Type: application/json` استفاده می‌کنید، در Body نمونه‌ی JSON قرار دهید.

## خروجی گرفتن و وارد کردن

- از صفحه‌ی نمایش سند، گزینه‌ی **Export / Download** برای دریافت JSON استفاده کنید.
- با **Import** می‌توانید کالکشن‌های JSON یا فرمت‌های پشتیبانی‌شده را وارد کنید.

## چند-اجاره‌گی (Tenant Scoping)

در این پروژه مدل `Filamat\IamSuite\Models\ApiDoc` استفاده شده است که `tenant_id` را دارد و به‌صورت خودکار Tenant Scope اعمال می‌شود. بنابراین:
- در پنل Tenant فقط اسناد همان Tenant نمایش داده می‌شود.
- در پنل Super Admin، محدودیت Tenant قابل دور زدن است (بر اساس تنظیمات Filamat IAM).

## دسترسی‌ها و کنترل امنیت

این پلاگین به‌صورت پیش‌فرض Policy جداگانه‌ای ندارد. برای کنترل دسترسی:
- یک Policy برای مدل `ApiDoc` بسازید و به IAM متصل کنید.
- یا از Policyهای Filament برای Resource استفاده کنید.
- نقش‌ها و مجوزها را از بخش تنظیمات دسترسی در Filamat IAM تعریف کنید.

کلیدهای مجوز پیشنهادی:
- `api.docs.view` (مشاهده مستندات)
- `api.docs.manage` (ایجاد/ویرایش/حذف مستندات)
- به‌صورت پیش‌فرض، اگر این‌ها را نداشتید، `api.view` و `api.manage` نیز پذیرفته می‌شوند.

## عیب‌یابی سریع

- اگر Resource دیده نمی‌شود:
  - `php artisan optimize:clear`
  - بررسی کنید پلاگین در PanelProvider ثبت شده باشد.
- اگر جدول‌ها وجود ندارند:
  - `php artisan migrate`
- اگر با کش‌های قدیمی مشکل دارید:
  - `php artisan config:clear`
  - `php artisan view:clear`

## نکات پیشنهادی

- برای هر محیط (dev/prod) هدرهای پیش‌فرض را دقیق تنظیم کنید.
- برای هر Tenant یک مجموعه مستندات جدا نگهداری کنید.
- از Export منظم برای بکاپ و انتقال استفاده کنید.
