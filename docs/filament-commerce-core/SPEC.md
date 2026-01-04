# SPEC — filament-commerce-core

## معرفی
- پکیج: haida/filament-commerce-core
- توضیح: Commerce core domain kernel for Hub.
- Service Provider: Haida\FilamentCommerceCore\FilamentCommerceCoreServiceProvider
- Filament Plugin: Haida\FilamentCommerceCore\FilamentCommerceCorePlugin (id: filament-commerce-core)

## دامنه و قابلیت‌ها
- مدل‌ها:
- CommerceBrand.php
- CommerceCategory.php
- CommerceComplianceDigest.php
- CommerceCustomer.php
- CommerceException.php
- CommerceFraudRule.php
- CommerceInventoryItem.php
- CommercePrice.php
- CommercePriceList.php
- CommerceProduct.php
- CommerceStockMove.php
- CommerceVariant.php
- منابع Filament:
- src/Filament/Resources/CommerceBrandResource.php
- src/Filament/Resources/CommerceCategoryResource.php
- src/Filament/Resources/CommerceCustomerResource.php
- src/Filament/Resources/CommerceExceptionResource.php
- src/Filament/Resources/CommerceFraudRuleResource.php
- src/Filament/Resources/CommerceInventoryItemResource.php
- src/Filament/Resources/CommercePriceListResource.php
- src/Filament/Resources/CommerceProductResource.php
- کنترلرها/API:
- Api/V1/ApiController.php
- Api/V1/CatalogSnapshotController.php
- Api/V1/InventorySnapshotController.php
- Api/V1/OpenApiController.php
- Api/V1/PricingSnapshotController.php
- Jobs/Queue:
- ندارد
- Policyها:
- CommerceBrandPolicy.php
- CommerceCategoryPolicy.php
- CommerceComplianceDigestPolicy.php
- CommerceCustomerPolicy.php
- CommerceExceptionPolicy.php
- CommerceFraudRulePolicy.php
- CommerceInventoryItemPolicy.php
- CommercePriceListPolicy.php
- CommercePricePolicy.php
- CommerceProductPolicy.php
- CommerceStockMovePolicy.php
- CommerceVariantPolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: بله
- TenantContext در کد: بله
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): commerce.catalog.view, commerce.inventory.view, commerce.pricing.view

## مدل داده
- Migrations:
- 2026_01_02_000001_create_commerce_core_tables.php
- 2026_01_03_000007_create_commerce_compliance_tables.php
- 2026_01_03_000008_create_commerce_compliance_digest_tables.php
- جدول‌ها:
- ندارد
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: v1
- OpenAPI: دارای مسیر /openapi
- جزئیات: `docs/filament-commerce-core/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-commerce-core/config/filament-commerce-core.php
- کلیدهای env مرتبط:
- ندارد

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت شده در TenantPanelProvider
