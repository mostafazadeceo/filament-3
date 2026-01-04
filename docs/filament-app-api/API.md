# API — filament-app-api

## استانداردها
- مسیر پایه: `/api/v1/<module>/...` (طبق الگوی پروژه)
- Middleware عمومی: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, و `filamat-iam.scope:<scope>`
- Rate limit پیش‌فرض: `60,1` مگر اینکه در config تغییر داده شده باشد.

## اندپوینت‌ها
| Method | Path | Scope | Permission | Rate Limit | Payload |
| --- | --- | --- | --- | --- | --- |
| POST | /api/v1/app/auth/login | [ASSUMPTION] | [ASSUMPTION] | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/app/auth/refresh | [ASSUMPTION] | [ASSUMPTION] | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/app/auth/logout | app.view | app.view | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/app/auth/me | app.view | app.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/app/tenant/current | app.tenant.view | app.tenant.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/app/tenant/switch | app.tenant.switch | app.tenant.switch | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/app/capabilities | app.view | app.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/app/config | app.config.view | app.config.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/app/sync/push | app.sync | app.sync | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/app/sync/pull | app.sync | app.sync | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/app/sync/conflicts | app.sync | app.sync | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/app/devices | app.device.manage | app.device.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/app/devices/{device}/tokens | app.device.manage | app.device.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| DELETE | /api/v1/app/devices/{device} | app.device.manage | app.device.manage | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/app/notifications | app.notification.view | app.notification.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/app/notifications/{notification}/read | app.notification.manage | app.notification.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/app/tickets | support.ticket.view | support.ticket.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/app/tickets | support.ticket.manage | support.ticket.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/app/tickets/{ticket}/messages | support.message.view | support.message.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/app/tickets/{ticket}/messages | support.message.manage | support.message.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/app/tickets/{ticket}/attachments | support.attachment.manage | support.attachment.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/app/realtime/signals | app.realtime.signal | app.realtime.signal | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/app/realtime/signals | app.realtime.signal | app.realtime.signal | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/app/openapi | app.view | app.view | [ASSUMPTION] 60,1 | - |

## OpenAPI
- در صورت وجود مسیر `/openapi`، خروجی از Filament API Docs Builder منتشر می‌شود.
