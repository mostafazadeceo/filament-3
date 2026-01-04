# SPEC — filament-notify-bale

## معرفی
- پکیج: haida/filament-notify-bale
- توضیح: Bale messenger channel for Filament Notify.
- Service Provider: Haida\FilamentNotify\Bale\FilamentNotifyBaleServiceProvider
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
- جزئیات: `docs/filament-notify-bale/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-notify-bale/config/filament-notify-bale.php
- کلیدهای env مرتبط:
- BALE_BASE_URL
- BALE_BOT_TOKEN
- BALE_DEFAULT_CHAT_ID

## استقرار در پنل‌ها
- Admin Panel: Plugin ندارد
- Tenant Panel: Plugin ندارد
