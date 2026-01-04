# SPEC — filament-marketplace-connectors

## معرفی
- پکیج: haida/filament-marketplace-connectors
- توضیح: Marketplace connector framework (Amazon/eBay skeletons).
- Service Provider: Haida\FilamentMarketplaceConnectors\FilamentMarketplaceConnectorsServiceProvider
- Filament Plugin: Haida\FilamentMarketplaceConnectors\FilamentMarketplaceConnectorsPlugin (id: filament-marketplace-connectors)

## دامنه و قابلیت‌ها
- مدل‌ها:
- MarketplaceConnector.php
- MarketplaceRateLimit.php
- MarketplaceSyncJob.php
- MarketplaceSyncLog.php
- MarketplaceToken.php
- منابع Filament:
- src/Filament/Resources/MarketplaceConnectorResource.php
- src/Filament/Resources/MarketplaceSyncJobResource.php
- کنترلرها/API:
- Api/V1/ConnectorController.php
- Api/V1/OpenApiController.php
- Api/V1/SyncController.php
- Jobs/Queue:
- SyncConnectorJob.php
- Policyها:
- MarketplaceConnectorPolicy.php
- MarketplaceSyncJobPolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: بله
- TenantContext در کد: [ASSUMPTION] نیازمند بررسی
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): marketplace.connectors.manage, marketplace.connectors.sync

## مدل داده
- Migrations:
- 2026_01_02_000006_create_marketplace_connector_tables.php
- جدول‌ها:
- ندارد
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: v1
- OpenAPI: دارای مسیر /openapi
- جزئیات: `docs/filament-marketplace-connectors/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-marketplace-connectors/config/filament-marketplace-connectors.php
- کلیدهای env مرتبط:
- ندارد

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت شده در TenantPanelProvider
