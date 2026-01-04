# SPEC — filament-relograde

## معرفی
- پکیج: haida/filament-relograde
- توضیح: Filament plugin for Relograde API integration.
- Service Provider: Haida\FilamentRelograde\RelogradeServiceProvider
- Filament Plugin: Haida\FilamentRelograde\RelogradePlugin (id: relograde)

## دامنه و قابلیت‌ها
- مدل‌ها:
- RelogradeAccount.php
- RelogradeAlert.php
- RelogradeApiLog.php
- RelogradeAuditLog.php
- RelogradeBrand.php
- RelogradeBrandOption.php
- RelogradeConnection.php
- RelogradeOrder.php
- RelogradeOrderItem.php
- RelogradeOrderLine.php
- RelogradeProduct.php
- RelogradeWebhookEvent.php
- منابع Filament:
- src/Resources/RelogradeAccountResource.php
- src/Resources/RelogradeAlertResource.php
- src/Resources/RelogradeApiLogResource.php
- src/Resources/RelogradeAuditLogResource.php
- src/Resources/RelogradeBrandResource.php
- src/Resources/RelogradeConnectionResource.php
- src/Resources/RelogradeOrderResource.php
- src/Resources/RelogradeProductResource.php
- src/Resources/RelogradeWebhookEventResource.php
- کنترلرها/API:
- RelogradeWebhookController.php
- Jobs/Queue:
- CheckLowBalanceAlertsJob.php
- PollPendingOrdersJob.php
- ProcessWebhookEventJob.php
- SyncAccountsJob.php
- SyncBrandsJob.php
- SyncProductsJob.php
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
- 2025_01_01_000001_create_relograde_connections_table.php
- 2025_01_01_000002_create_relograde_brands_table.php
- 2025_01_01_000003_create_relograde_brand_options_table.php
- 2025_01_01_000004_create_relograde_products_table.php
- 2025_01_01_000005_create_relograde_accounts_table.php
- 2025_01_01_000006_create_relograde_orders_table.php
- 2025_01_01_000007_create_relograde_order_items_table.php
- 2025_01_01_000008_create_relograde_order_lines_table.php
- 2025_01_01_000009_create_relograde_webhook_events_table.php
- 2025_01_01_000010_create_relograde_api_logs_table.php
- 2025_01_01_000011_create_relograde_audit_logs_table.php
- 2025_01_01_000012_create_relograde_alerts_table.php
- 2025_01_01_000013_alter_redeem_value_columns_to_string.php
- جدول‌ها:
- relograde_accounts
- relograde_alerts
- relograde_api_logs
- relograde_audit_logs
- relograde_brand_options
- relograde_brands
- relograde_connections
- relograde_order_items
- relograde_order_lines
- relograde_orders
- relograde_products
- relograde_webhook_events
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: relograde
- OpenAPI: ندارد/نامشخص
- جزئیات: `docs/filament-relograde/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-relograde/config/relograde.php
- کلیدهای env مرتبط:
- RELOGRADE_API_VERSION
- RELOGRADE_BASE_URL

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت نشده در TenantPanelProvider
