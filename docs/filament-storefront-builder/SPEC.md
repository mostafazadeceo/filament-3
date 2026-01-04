# SPEC — filament-storefront-builder

## معرفی
- پکیج: haida/filament-storefront-builder
- توضیح: Storefront builder for Hub.
- Service Provider: Haida\FilamentStorefrontBuilder\FilamentStorefrontBuilderServiceProvider
- Filament Plugin: Haida\FilamentStorefrontBuilder\FilamentStorefrontBuilderPlugin (id: filament-storefront-builder)

## دامنه و قابلیت‌ها
- مدل‌ها:
- StoreBlock.php
- StoreMenu.php
- StoreMenuItem.php
- StorePage.php
- StorePageVersion.php
- StoreRedirect.php
- StoreTheme.php
- منابع Filament:
- src/Filament/Resources/StoreBlockResource.php
- src/Filament/Resources/StoreMenuResource.php
- src/Filament/Resources/StorePageResource.php
- src/Filament/Resources/StoreRedirectResource.php
- src/Filament/Resources/StoreThemeResource.php
- کنترلرها/API:
- Api/V1/OpenApiController.php
- Web/PublicBlockController.php
- Web/PublicMenuController.php
- Web/PublicPageController.php
- Web/PublicThemeController.php
- Web/SitemapController.php
- Jobs/Queue:
- ندارد
- Policyها:
- StoreBlockPolicy.php
- StoreMenuPolicy.php
- StorePagePolicy.php
- StoreRedirectPolicy.php
- StoreThemePolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: بله
- TenantContext در کد: [ASSUMPTION] نیازمند بررسی
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): storebuilder.view

## مدل داده
- Migrations:
- 2026_01_02_000004_create_storefront_builder_tables.php
- جدول‌ها:
- ندارد
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: blocks, menus, pages, sitemap.xml, theme, v1
- OpenAPI: دارای مسیر /openapi
- جزئیات: `docs/filament-storefront-builder/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-storefront-builder/config/filament-storefront-builder.php
- کلیدهای env مرتبط:
- ندارد

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت شده در TenantPanelProvider
