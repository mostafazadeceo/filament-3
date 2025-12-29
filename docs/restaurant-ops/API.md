# Restaurant Ops API

## Base
- `/api/v1/restaurant-ops`
- Auth: `ApiKeyAuth` + `ApiAuth` + `ResolveTenant`
- Rate limit: `config('filament-restaurant-ops.api.rate_limit')`

## Endpoints (MVP)
- `GET /suppliers` / `POST /suppliers`
- `GET /items` / `POST /items`
- `GET /warehouses` / `POST /warehouses`
- `GET /purchase-requests` / `POST /purchase-requests`
- `GET /purchase-orders` / `POST /purchase-orders`
- `GET /goods-receipts` / `POST /goods-receipts`
- `POST /goods-receipts/{goods_receipt}/post`
- `GET /inventory-docs` / `POST /inventory-docs`
- `POST /inventory-docs/{inventory_doc}/post`
- `GET /recipes` / `POST /recipes`
- `GET /menu-items` / `POST /menu-items`
- `GET /menu-sales` / `POST /menu-sales`
- `POST /menu-sales/{menu_sale}/post`
- `GET /openapi`

## Webhooks (آینده)
- `purchase_order.sent`
- `goods_receipt.posted`
- `inventory_doc.posted`
- `menu_sale.imported`
- `recipe.cost_updated`
