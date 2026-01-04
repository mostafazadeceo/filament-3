# API — filament-pos

## استانداردها
- مسیر پایه: `/api/v1/<module>/...` (طبق الگوی پروژه)
- Middleware عمومی: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, و `filamat-iam.scope:<scope>`
- Rate limit پیش‌فرض: `60,1` مگر اینکه در config تغییر داده شده باشد.

## اندپوینت‌ها
| Method | Path | Scope | Permission | Rate Limit | Payload |
| --- | --- | --- | --- | --- | --- |
| GET | /api/v1/filament-pos/sync/snapshot | pos.use | pos.use | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/filament-pos/sync/delta | pos.use | pos.use | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/filament-pos/sync/outbox | pos.use | pos.use | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/filament-pos/sales | pos.use | pos.use | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/filament-pos/openapi | pos.view | pos.view | [ASSUMPTION] 60,1 | - |

## OpenAPI
- در صورت وجود مسیر `/openapi`، خروجی از Filament API Docs Builder منتشر می‌شود.
