# SPEC — site-builder-core

## معرفی
- پکیج: haida/site-builder-core
- توضیح: Core site builder domain models and Filament plugin.
- Service Provider: Haida\SiteBuilderCore\SiteBuilderCoreServiceProvider
- Filament Plugin: Haida\SiteBuilderCore\SiteBuilderCorePlugin (id: site-builder-core)

## دامنه و قابلیت‌ها
- مدل‌ها:
- Site.php
- SiteBranding.php
- SitePublishHistory.php
- منابع Filament:
- src/Filament/Resources/SiteResource.php
- کنترلرها/API:
- ندارد
- Jobs/Queue:
- ندارد
- Policyها:
- SitePolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: بله
- TenantContext در کد: [ASSUMPTION] نیازمند بررسی
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): ندارد

## مدل داده
- Migrations:
- 2025_12_30_000006_create_site_builder_core_tables.php
- جدول‌ها:
- ندارد
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: ندارد
- OpenAPI: ندارد/نامشخص
- جزئیات: `docs/site-builder-core/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/site-builder-core/config/site-builder-core.php
- کلیدهای env مرتبط:
- ندارد

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت شده در TenantPanelProvider
