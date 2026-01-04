# SPEC — filament-threecx

## معرفی
- پکیج: haida/filament-threecx
- توضیح: Filament v4 3CX integration module.
- Service Provider: Haida\FilamentThreeCx\FilamentThreeCxServiceProvider
- Filament Plugin: ندارد

## دامنه و قابلیت‌ها
- مدل‌ها:
- ThreeCxApiAuditLog.php
- ThreeCxCallLog.php
- ThreeCxContact.php
- ThreeCxInstance.php
- ThreeCxSyncCursor.php
- ThreeCxTokenCache.php
- منابع Filament:
- src/Filament/Resources/ThreeCxCallLogResource.php
- src/Filament/Resources/ThreeCxContactResource.php
- src/Filament/Resources/ThreeCxInstanceResource.php
- کنترلرها/API:
- Api/V1/ApiController.php
- Api/V1/CrmController.php
- Api/V1/OpenApiController.php
- Jobs/Queue:
- SyncCallHistoryJob.php
- SyncChatHistoryJob.php
- SyncContactsJob.php
- Policyها:
- ThreeCxApiAuditLogPolicy.php
- ThreeCxCallLogPolicy.php
- ThreeCxContactPolicy.php
- ThreeCxInstancePolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: بله
- TenantContext در کد: بله
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): threecx.view

## مدل داده
- Migrations:
- 2025_12_30_000020_create_threecx_tables.php
- جدول‌ها:
- ندارد
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: v1
- OpenAPI: دارای مسیر /openapi
- جزئیات: `docs/filament-threecx/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-threecx/config/filament-threecx.php
- کلیدهای env مرتبط:
- THREECX_API_EXPLORER_ENABLED
- THREECX_API_EXPLORER_MAX_BODY
- THREECX_API_RATE_LIMIT
- THREECX_AUTH_CLIENT_AUTH
- THREECX_AUTH_GRANT_TYPE
- THREECX_AUTH_TOKEN_PATH
- THREECX_CACHE_DB_FALLBACK
- THREECX_CACHE_ENABLED
- THREECX_CACHE_STORE
- THREECX_CALL_CONTROL_BASE_PATH
- THREECX_CALL_CONTROL_CALLS_PATH
- THREECX_CALL_CONTROL_DN_STATE_PATH
- THREECX_CALL_CONTROL_ENABLED
- THREECX_CALL_CONTROL_ENTITIES_PATH
- THREECX_CALL_CONTROL_FROM_KEY
- THREECX_CALL_CONTROL_TERMINATE_PATH
- THREECX_CALL_CONTROL_TO_KEY
- THREECX_CALL_CONTROL_TRANSFER_PATH
- THREECX_CRM_AUTH_MODE
- THREECX_CRM_CONNECTOR_ENABLED
- THREECX_CRM_INSTANCE_PARAM
- THREECX_CRM_KEY_HEADER
- THREECX_CRM_MAX_RESULTS
- THREECX_CRM_RATE_LIMIT
- THREECX_CRM_STORE_CALL_RAW
- THREECX_CRM_STORE_CHAT_RAW
- THREECX_CRM_STORE_RAW
- THREECX_CRM_TENANT_PARAM
- THREECX_HTTP_RETRY_SLEEP
- THREECX_HTTP_RETRY_TIMES
- THREECX_HTTP_TIMEOUT
- THREECX_HTTP_USER_AGENT
- THREECX_LOGGING_ENABLED
- THREECX_LOG_REDACT_REQUEST
- THREECX_LOG_REDACT_RESPONSE
- THREECX_NOTIFY_PANEL
- THREECX_OPENAPI_CACHE_ENABLED
- THREECX_OPENAPI_PATH
- THREECX_OPENAPI_TTL
- THREECX_RATE_MAX
- THREECX_RATE_SECONDS
- THREECX_RETENTION_API_AUDIT_DAYS
- THREECX_RETENTION_CALL_LOGS_DAYS
- THREECX_RETENTION_SYNC_DAYS
- THREECX_SCOPE_CALL_CONTROL
- THREECX_SCOPE_XAPI
- THREECX_SYNC_BATCH
- THREECX_SYNC_STORE_RAW
- THREECX_WS_LISTENER_ENABLED
- THREECX_XAPI_BASE_PATH
- THREECX_XAPI_CALL_HISTORY_PATH
- THREECX_XAPI_CAPABILITIES_PATH
- THREECX_XAPI_CHAT_HISTORY_PATH
- THREECX_XAPI_CONTACTS_PATH
- THREECX_XAPI_ENABLED
- THREECX_XAPI_HEALTH_PATH
- THREECX_XAPI_VERSION_PATH

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت شده در TenantPanelProvider
