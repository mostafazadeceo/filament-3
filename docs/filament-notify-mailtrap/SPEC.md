# SPEC — filament-notify-mailtrap

## معرفی
- پکیج: haida/filament-notify-mailtrap
- توضیح: Mailtrap channel for Filament Notify.
- Service Provider: Haida\FilamentNotify\Mailtrap\FilamentNotifyMailtrapServiceProvider
- Filament Plugin: ندارد

## دامنه و قابلیت‌ها
- مدل‌ها:
- ندارد
- منابع Filament:
- ندارد
- کنترلرها/API:
- ندارد
- Jobs/Queue:
- ندارد
- Policyها:
- ندارد

## Tenancy و IAM
- BelongsToTenant در کد: [ASSUMPTION] نیازمند بررسی/افزودن
- TenantContext در کد: [ASSUMPTION] نیازمند بررسی
- IamAuthorization::allows در کد: [ASSUMPTION] نیازمند بررسی
- Capability Registry: [ASSUMPTION] در صورت وجود باید ثبت شود
- Scopeها (API): ندارد

## مدل داده
- Migrations:
- ندارد
- جدول‌ها:
- ندارد
- ایندکس‌ها: [ASSUMPTION] نیازمند مرور مهاجرت‌ها

## API
- مسیر پایه: ندارد
- OpenAPI: ندارد/نامشخص
- جزئیات: `docs/filament-notify-mailtrap/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-notify-mailtrap/config/filament-notify-mailtrap.php
- کلیدهای env مرتبط:
- MAILTRAP_FROM_ADDRESS
- MAILTRAP_FROM_NAME
- MAILTRAP_SEND_API_TOKEN
- MAILTRAP_SEND_BASE_URL
- MAILTRAP_SEND_CATEGORY

## استقرار در پنل‌ها
- Admin Panel: Plugin ندارد
- Tenant Panel: Plugin ندارد
