# API — tenancy-domains

## استانداردها
- مسیر پایه: `/api/v1/<module>/...` (طبق الگوی پروژه)
- Middleware عمومی: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, و `filamat-iam.scope:<scope>`
- Rate limit پیش‌فرض: `60,1` مگر اینکه در config تغییر داده شده باشد.

## اندپوینت‌ها
| Method | Path | Scope | Permission | Rate Limit | Payload |
| --- | --- | --- | --- | --- | --- |
| GET | /api/v1/tenancy-domains/domains | site.domain.view | site.domain.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/tenancy-domains/domains | site.domain.manage | site.domain.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/tenancy-domains/domains/{domain} | site.domain.view | site.domain.view | [ASSUMPTION] 60,1 | - |
| PATCH | /api/v1/tenancy-domains/domains/{domain} | site.domain.manage | site.domain.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| DELETE | /api/v1/tenancy-domains/domains/{domain} | site.domain.manage | site.domain.manage | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/tenancy-domains/domains/{domain}/request-verification | site.domain.manage | site.domain.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/tenancy-domains/domains/{domain}/verify | site.domain.manage | site.domain.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/tenancy-domains/domains/{domain}/request-tls | site.domain.manage | site.domain.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |

## OpenAPI
- در صورت وجود مسیر `/openapi`، خروجی از Filament API Docs Builder منتشر می‌شود.
