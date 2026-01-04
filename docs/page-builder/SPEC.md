# SPEC — page-builder

## معرفی
- پکیج: haida/page-builder
- توضیح: Template-driven page builder for Site OS.
- Service Provider: Haida\PageBuilder\PageBuilderServiceProvider
- Filament Plugin: Haida\PageBuilder\PageBuilderPlugin (id: page-builder)

## دامنه و قابلیت‌ها
- مدل‌ها:
- PageTemplate.php
- PageTemplateRevision.php
- منابع Filament:
- src/Filament/Resources/PageTemplateResource.php
- کنترلرها/API:
- ندارد
- Jobs/Queue:
- ندارد
- Policyها:
- PageTemplatePolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: بله
- TenantContext در کد: [ASSUMPTION] نیازمند بررسی
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): ندارد

## مدل داده
- Migrations:
- 2025_12_30_000007_create_page_builder_tables.php
- جدول‌ها:
- ندارد
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: ندارد
- OpenAPI: ندارد/نامشخص
- جزئیات: `docs/page-builder/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/page-builder/config/page-builder.php
- کلیدهای env مرتبط:
- ندارد

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت شده در TenantPanelProvider
