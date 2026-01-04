# SPEC — filament-providers-esim-go

## معرفی
- پکیج: haida/filament-providers-esim-go
- توضیح: Filament UI for eSIM Go provider.
- Service Provider: Haida\FilamentProvidersEsimGo\FilamentProvidersEsimGoServiceProvider
- Filament Plugin: Haida\FilamentProvidersEsimGo\ProvidersEsimGoPlugin (id: providers-esim-go)

## دامنه و قابلیت‌ها
- مدل‌ها:
- ندارد
- منابع Filament:
- src/Resources/EsimGoCatalogueSnapshotResource.php
- src/Resources/EsimGoConnectionResource.php
- src/Resources/EsimGoEsimResource.php
- src/Resources/EsimGoOrderResource.php
- src/Resources/EsimGoProductResource.php
- کنترلرها/API:
- ندارد
- Jobs/Queue:
- ندارد
- Policyها:
- ندارد

## Tenancy و IAM
- BelongsToTenant در کد: [ASSUMPTION] نیازمند بررسی/افزودن
- TenantContext در کد: [ASSUMPTION] نیازمند بررسی
- IamAuthorization::allows در کد: بله
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
- جزئیات: `docs/filament-providers-esim-go/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-providers-esim-go/config/filament-providers-esim-go.php
- کلیدهای env مرتبط:
- ندارد

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت شده در TenantPanelProvider
