# SPEC — theme-engine

## معرفی
- پکیج: haida/theme-engine
- توضیح: Theme registry and relograde-v1 assets for Site OS.
- Service Provider: Haida\ThemeEngine\ThemeEngineServiceProvider
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
- جزئیات: `docs/theme-engine/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/theme-engine/config/theme-engine.php
- کلیدهای env مرتبط:
- ندارد

## استقرار در پنل‌ها
- Admin Panel: Plugin ندارد
- Tenant Panel: Plugin ندارد
