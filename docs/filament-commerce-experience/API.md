# API — filament-commerce-experience

## استانداردها
- مسیر پایه: `/api/v1/<module>/...` (طبق الگوی پروژه)
- Middleware عمومی: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, و `filamat-iam.scope:<scope>`
- Rate limit پیش‌فرض: `60,1` مگر اینکه در config تغییر داده شده باشد.

## اندپوینت‌ها
| Method | Path | Scope | Permission | Rate Limit | Payload |
| --- | --- | --- | --- | --- | --- |
| GET | /api/v1/filament-commerce-experience/reviews | experience.reviews.view | experience.reviews.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/filament-commerce-experience/questions | experience.reviews.view | experience.reviews.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/filament-commerce-experience/csat | experience.csat.manage | experience.csat.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/filament-commerce-experience/buy-now | experience.buy_now.manage | experience.buy_now.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/filament-commerce-experience/openapi | experience.reviews.view | experience.reviews.view | [ASSUMPTION] 60,1 | - |

## OpenAPI
- در صورت وجود مسیر `/openapi`، خروجی از Filament API Docs Builder منتشر می‌شود.
