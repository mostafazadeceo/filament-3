# API — filament-currency-rates

## استانداردها
- مسیر پایه: `/api/v1/<module>/...` (طبق الگوی پروژه)
- Middleware عمومی: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, و `filamat-iam.scope:<scope>`
- Rate limit پیش‌فرض: `60,1` مگر اینکه در config تغییر داده شده باشد.

## اندپوینت‌ها
| Method | Path | Scope | Permission | Rate Limit | Payload |
| --- | --- | --- | --- | --- | --- |
| GET | / | [ASSUMPTION] | [ASSUMPTION] | [ASSUMPTION] 60,1 | - |
| GET | /currency-rates/{code} | [ASSUMPTION] | [ASSUMPTION] | [ASSUMPTION] 60,1 | - |

## OpenAPI
- در صورت وجود مسیر `/openapi`، خروجی از Filament API Docs Builder منتشر می‌شود.
