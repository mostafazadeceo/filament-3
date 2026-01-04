# API — filament-commerce-core

## استانداردها
- مسیر پایه: `/api/v1/<module>/...` (طبق الگوی پروژه)
- Middleware عمومی: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, و `filamat-iam.scope:<scope>`
- Rate limit پیش‌فرض: `60,1` مگر اینکه در config تغییر داده شده باشد.

## اندپوینت‌ها
| Method | Path | Scope | Permission | Rate Limit | Payload |
| --- | --- | --- | --- | --- | --- |
| GET | /api/v1/filament-commerce-core/snapshots/catalog | commerce.catalog.view | commerce.catalog.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/filament-commerce-core/snapshots/pricing | commerce.pricing.view | commerce.pricing.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/filament-commerce-core/snapshots/inventory | commerce.inventory.view | commerce.inventory.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/filament-commerce-core/openapi | commerce.catalog.view | commerce.catalog.view | [ASSUMPTION] 60,1 | - |

## OpenAPI
- در صورت وجود مسیر `/openapi`، خروجی از Filament API Docs Builder منتشر می‌شود.
