# API — providers-esim-go-core

## استانداردها
- مسیر پایه: `/api/v1/<module>/...` (طبق الگوی پروژه)
- Middleware عمومی: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, و `filamat-iam.scope:<scope>`
- Rate limit پیش‌فرض: `60,1` مگر اینکه در config تغییر داده شده باشد.

## اندپوینت‌ها
| Method | Path | Scope | Permission | Rate Limit | Payload |
| --- | --- | --- | --- | --- | --- |
| GET | /api/v1/providers/esim-go/connections | esim_go.connection.view | esim_go.connection.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/providers/esim-go/products | esim_go.product.view | esim_go.product.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/providers/esim-go/products/{product} | esim_go.product.view | esim_go.product.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/providers/esim-go/orders | esim_go.order.view | esim_go.order.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/providers/esim-go/orders/{order} | esim_go.order.view | esim_go.order.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/providers/esim-go/sync | esim_go.catalogue.sync | esim_go.catalogue.sync | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/providers/esim-go/openapi | esim_go.product.view | esim_go.product.view | [ASSUMPTION] 60,1 | - |

## OpenAPI
- در صورت وجود مسیر `/openapi`، خروجی از Filament API Docs Builder منتشر می‌شود.
