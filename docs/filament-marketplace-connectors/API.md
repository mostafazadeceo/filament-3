# API — filament-marketplace-connectors

## استانداردها
- مسیر پایه: `/api/v1/<module>/...` (طبق الگوی پروژه)
- Middleware عمومی: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, و `filamat-iam.scope:<scope>`
- Rate limit پیش‌فرض: `60,1` مگر اینکه در config تغییر داده شده باشد.

## اندپوینت‌ها
| Method | Path | Scope | Permission | Rate Limit | Payload |
| --- | --- | --- | --- | --- | --- |
| GET | /api/v1/filament-marketplace-connectors/connectors | marketplace.connectors.manage | marketplace.connectors.manage | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/filament-marketplace-connectors/connectors/{connector}/sync | marketplace.connectors.sync | marketplace.connectors.sync | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/filament-marketplace-connectors/openapi | marketplace.connectors.manage | marketplace.connectors.manage | [ASSUMPTION] 60,1 | - |

## OpenAPI
- در صورت وجود مسیر `/openapi`، خروجی از Filament API Docs Builder منتشر می‌شود.
