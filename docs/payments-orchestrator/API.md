# API — payments-orchestrator

## استانداردها
- مسیر پایه: `/api/v1/<module>/...` (طبق الگوی پروژه)
- Middleware عمومی: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, و `filamat-iam.scope:<scope>`
- Rate limit پیش‌فرض: `60,1` مگر اینکه در config تغییر داده شده باشد.

## اندپوینت‌ها
| Method | Path | Scope | Permission | Rate Limit | Payload |
| --- | --- | --- | --- | --- | --- |
| POST | /api/v1/commerce-payments/intents | commerce.payment.manage | commerce.payment.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/commerce-payments/intents/{intent} | commerce.payment.view | commerce.payment.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/commerce-payments/openapi | commerce.payment.view | commerce.payment.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/commerce-payments/webhooks/{provider} | [ASSUMPTION] | [ASSUMPTION] | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |

## OpenAPI
- در صورت وجود مسیر `/openapi`، خروجی از Filament API Docs Builder منتشر می‌شود.
