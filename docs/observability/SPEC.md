# SPEC — observability

## معرفی
- پکیج: haida/observability
- توضیح: Correlation IDs and logging context for observability.
- Service Provider: Haida\Observability\ObservabilityServiceProvider
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
- جزئیات: `docs/observability/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/observability/config/observability.php
- کلیدهای env مرتبط:
- OBSERVABILITY_CORRELATION_HEADER
- OBSERVABILITY_ENABLED

## استقرار در پنل‌ها
- Admin Panel: Plugin ندارد
- Tenant Panel: Plugin ندارد
