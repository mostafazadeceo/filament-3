# API — content-cms

## استانداردها
- مسیر پایه: `/api/v1/<module>/...` (طبق الگوی پروژه)
- Middleware عمومی: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, و `filamat-iam.scope:<scope>`
- Rate limit پیش‌فرض: `60,1` مگر اینکه در config تغییر داده شده باشد.

## اندپوینت‌ها
| Method | Path | Scope | Permission | Rate Limit | Payload |
| --- | --- | --- | --- | --- | --- |
| GET | /api/v1/content-cms/openapi | cms.page.view | cms.page.view | [ASSUMPTION] 60,1 | - |
| GET | /sitemap.xml | [ASSUMPTION] | [ASSUMPTION] | [ASSUMPTION] 60,1 | - |
| GET | / | [ASSUMPTION] | [ASSUMPTION] | [ASSUMPTION] 60,1 | - |
| GET | /{slug} | [ASSUMPTION] | [ASSUMPTION] | [ASSUMPTION] 60,1 | - |

## OpenAPI
- در صورت وجود مسیر `/openapi`، خروجی از Filament API Docs Builder منتشر می‌شود.
