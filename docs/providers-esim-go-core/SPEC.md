# SPEC — providers-esim-go-core

## معرفی
- پکیج: haida/providers-esim-go-core
- توضیح: Core integration for eSIM Go provider (client, models, services).
- Service Provider: Haida\ProvidersEsimGoCore\ProvidersEsimGoCoreServiceProvider
- Filament Plugin: ندارد

## دامنه و قابلیت‌ها
- مدل‌ها:
- EsimGoCallback.php
- EsimGoCatalogueSnapshot.php
- EsimGoConnection.php
- EsimGoEsim.php
- EsimGoInventoryUsage.php
- EsimGoOrder.php
- EsimGoProduct.php
- منابع Filament:
- ندارد
- کنترلرها/API:
- Api/V1/EsimGoConnectionController.php
- Api/V1/EsimGoOrderController.php
- Api/V1/EsimGoProductController.php
- Api/V1/EsimGoSyncController.php
- Api/V1/OpenApiController.php
- Jobs/Queue:
- PollEsimGoAssignmentsJob.php
- Policyها:
- EsimGoCallbackPolicy.php
- EsimGoCatalogueSnapshotPolicy.php
- EsimGoConnectionPolicy.php
- EsimGoEsimPolicy.php
- EsimGoInventoryUsagePolicy.php
- EsimGoOrderPolicy.php
- EsimGoProductPolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: بله
- TenantContext در کد: بله
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): esim_go.catalogue.sync, esim_go.connection.view, esim_go.order.view, esim_go.product.view

## مدل داده
- Migrations:
- 2025_12_31_000001_create_esim_go_connections_table.php
- 2025_12_31_000002_create_esim_go_catalogue_snapshots_table.php
- 2025_12_31_000003_create_esim_go_products_table.php
- 2025_12_31_000004_create_esim_go_orders_table.php
- 2025_12_31_000005_create_esim_go_esims_table.php
- 2025_12_31_000006_create_esim_go_callbacks_table.php
- 2025_12_31_000007_create_esim_go_inventory_usages_table.php
- 2026_01_01_000007_add_countries_meta_to_esim_go_products_table.php
- جدول‌ها:
- ندارد
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: v1
- OpenAPI: دارای مسیر /openapi
- جزئیات: `docs/providers-esim-go-core/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/providers-esim-go-core/config/providers-esim-go-core.php
- کلیدهای env مرتبط:
- ESIM_GO_API_KEY_HEADER
- ESIM_GO_API_RATE_LIMIT
- ESIM_GO_BASE_URL
- ESIM_GO_CACHE_STORE
- ESIM_GO_FAKE
- ESIM_GO_FAKE_RUN_ID
- ESIM_GO_LOGGING_ENABLED
- ESIM_GO_NOTIFY_PANEL
- ESIM_GO_QUEUE
- ESIM_GO_REFUND_ENABLED
- ESIM_GO_REFUND_WINDOW_DAYS
- ESIM_GO_SANDBOX_BASE_URL

## استقرار در پنل‌ها
- Admin Panel: Plugin ندارد
- Tenant Panel: Plugin ندارد
