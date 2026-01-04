# API — commerce-checkout

## استانداردها
- مسیر پایه: `/api/v1/<module>/...` (طبق الگوی پروژه)
- Middleware عمومی: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, و `filamat-iam.scope:<scope>`
- Rate limit پیش‌فرض: `60,1` مگر اینکه در config تغییر داده شده باشد.

## اندپوینت‌ها
| Method | Path | Scope | Permission | Rate Limit | Payload |
| --- | --- | --- | --- | --- | --- |
| POST | /api/v1/commerce-checkout/carts/{cart}/items | commerce.cart.manage | commerce.cart.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| PATCH | /api/v1/commerce-checkout/cart-items/{item} | commerce.cart.manage | commerce.cart.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| DELETE | /api/v1/commerce-checkout/cart-items/{item} | commerce.cart.manage | commerce.cart.manage | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/commerce-checkout/checkout | commerce.checkout.create | commerce.checkout.create | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/commerce-checkout/openapi | commerce.cart.view | commerce.cart.view | [ASSUMPTION] 60,1 | - |

## OpenAPI
- در صورت وجود مسیر `/openapi`، خروجی از Filament API Docs Builder منتشر می‌شود.
