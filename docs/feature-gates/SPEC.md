# SPEC — feature-gates

## معرفی
- پکیج: haida/feature-gates
- توضیح: Central feature gate system for plan and tenant entitlements.
- Service Provider: Haida\FeatureGates\FeatureGatesServiceProvider
- Filament Plugin: ندارد

## دامنه و قابلیت‌ها
- مدل‌ها:
- PlanFeature.php
- TenantFeatureOverride.php
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
- 2025_12_30_000002_create_plan_features_table.php
- 2025_12_30_000003_create_tenant_feature_overrides_table.php
- جدول‌ها:
- ندارد
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: ندارد
- OpenAPI: ندارد/نامشخص
- جزئیات: `docs/feature-gates/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/feature-gates/config/feature-gates.php
- کلیدهای env مرتبط:
- ندارد

## استقرار در پنل‌ها
- Admin Panel: Plugin ندارد
- Tenant Panel: Plugin ندارد
