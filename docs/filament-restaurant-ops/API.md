# API — filament-restaurant-ops

## استانداردها
- مسیر پایه: `/api/v1/<module>/...` (طبق الگوی پروژه)
- Middleware عمومی: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, و `filamat-iam.scope:<scope>`
- Rate limit پیش‌فرض: `60,1` مگر اینکه در config تغییر داده شده باشد.

## اندپوینت‌ها
| Method | Path | Scope | Permission | Rate Limit | Payload |
| --- | --- | --- | --- | --- | --- |
| POST | /api/v1/restaurant-ops/goods-receipts/{goods_receipt}/post | restaurant.goods_receipt.post | restaurant.goods_receipt.post | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/restaurant-ops/inventory-docs/{inventory_doc}/post | restaurant.inventory_doc.post | restaurant.inventory_doc.post | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/restaurant-ops/menu-sales/{menu_sale}/post | restaurant.menu_sale.post | restaurant.menu_sale.post | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/restaurant-ops/openapi | [ASSUMPTION] | [ASSUMPTION] | [ASSUMPTION] 60,1 | - |

## OpenAPI
- در صورت وجود مسیر `/openapi`، خروجی از Filament API Docs Builder منتشر می‌شود.
