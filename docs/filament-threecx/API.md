# API — filament-threecx

## استانداردها
- مسیر پایه: `/api/v1/<module>/...` (طبق الگوی پروژه)
- Middleware عمومی: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, و `filamat-iam.scope:<scope>`
- Rate limit پیش‌فرض: `60,1` مگر اینکه در config تغییر داده شده باشد.

## اندپوینت‌ها
| Method | Path | Scope | Permission | Rate Limit | Payload |
| --- | --- | --- | --- | --- | --- |
| GET | /api/v1/threecx/openapi | threecx.view | threecx.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/threecx/lookup | [ASSUMPTION] | [ASSUMPTION] | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/threecx/search | [ASSUMPTION] | [ASSUMPTION] | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/threecx/contacts | [ASSUMPTION] | [ASSUMPTION] | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/threecx/journal/call | [ASSUMPTION] | [ASSUMPTION] | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/threecx/journal/chat | [ASSUMPTION] | [ASSUMPTION] | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |

## OpenAPI
- در صورت وجود مسیر `/openapi`، خروجی از Filament API Docs Builder منتشر می‌شود.
