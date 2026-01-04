# SPEC — filament-app-api

## معرفی
- پکیج: haida/filament-app-api
- توضیح: App API for Haida Hub mobile/web clients.
- Service Provider: Haida\FilamentAppApi\FilamentAppApiServiceProvider
- Filament Plugin: ندارد

## دامنه و قابلیت‌ها
- مدل‌ها:
- AppAttendanceRecord.php
- AppDevice.php
- AppDeviceToken.php
- AppRefreshToken.php
- AppSignalingMessage.php
- AppSupportMessage.php
- AppSupportTicket.php
- AppSyncChange.php
- AppTask.php
- منابع Filament:
- ندارد
- کنترلرها/API:
- Api/V1/AppConfigController.php
- Api/V1/AuthController.php
- Api/V1/CapabilityController.php
- Api/V1/DeviceController.php
- Api/V1/NotificationFeedController.php
- Api/V1/OpenApiController.php
- Api/V1/RealtimeSignalController.php
- Api/V1/SupportAttachmentController.php
- Api/V1/SupportMessageController.php
- Api/V1/SupportTicketController.php
- Api/V1/SyncController.php
- Api/V1/TenantController.php
- Jobs/Queue:
- ندارد
- Policyها:
- ندارد

## Tenancy و IAM
- BelongsToTenant در کد: بله
- TenantContext در کد: بله
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): app.config.view, app.device.manage, app.notification.manage, app.notification.view, app.realtime.signal, app.sync, app.tenant.switch, app.tenant.view, app.view, support.attachment.manage, support.message.manage, support.message.view, support.ticket.manage, support.ticket.view

## مدل داده
- Migrations:
- 2026_02_01_000001_create_app_devices_table.php
- 2026_02_01_000002_create_app_device_tokens_table.php
- 2026_02_01_000003_create_app_support_tables.php
- 2026_02_01_000004_create_app_sync_tables.php
- 2026_02_01_000005_create_app_refresh_tokens_table.php
- 2026_02_01_000006_create_app_tasks_table.php
- 2026_02_01_000007_create_app_attendance_records_table.php
- 2026_02_01_000008_create_app_signaling_messages_table.php
- جدول‌ها:
- ندارد
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: v1
- OpenAPI: دارای مسیر /openapi
- جزئیات: `docs/filament-app-api/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-app-api/config/filament-app-api.php
- کلیدهای env مرتبط:
- APP_REALTIME_WS_URL
- APP_TURN_SERVERS
- FILAMENT_APP_API_RATE_LIMIT
- FILAMENT_APP_API_REFRESH_TTL
- FILAMENT_APP_API_SYNC_PULL_LIMIT

## استقرار در پنل‌ها
- Admin Panel: Plugin ندارد
- Tenant Panel: Plugin ندارد
