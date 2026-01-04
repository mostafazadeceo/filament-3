# SPEC — commerce-catalog

## معرفی
- پکیج: haida/commerce-catalog
- توضیح: Commerce catalog module for Site OS.
- Service Provider: Haida\CommerceCatalog\CommerceCatalogServiceProvider
- Filament Plugin: Haida\CommerceCatalog\CommerceCatalogPlugin (id: commerce-catalog)

## دامنه و قابلیت‌ها
- مدل‌ها:
- CatalogCollection.php
- CatalogMedia.php
- CatalogProduct.php
- CatalogVariant.php
- منابع Filament:
- src/Filament/Resources/CatalogCollectionResource.php
- src/Filament/Resources/CatalogProductResource.php
- کنترلرها/API:
- Api/V1/ApiController.php
- Api/V1/CollectionController.php
- Api/V1/OpenApiController.php
- Api/V1/ProductController.php
- Jobs/Queue:
- ندارد
- Policyها:
- CatalogCollectionPolicy.php
- CatalogProductPolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: بله
- TenantContext در کد: بله
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): catalog.product.view

## مدل داده
- Migrations:
- 2025_12_30_000010_create_commerce_catalog_tables.php
- جدول‌ها:
- ندارد
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: v1
- OpenAPI: دارای مسیر /openapi
- جزئیات: `docs/commerce-catalog/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/commerce-catalog/config/commerce-catalog.php
- کلیدهای env مرتبط:
- ندارد

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت شده در TenantPanelProvider
