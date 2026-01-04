# API — Provider eSIM Go

Base: `/api/v1/providers/esim-go`

## Auth
- ApiKeyAuth + ApiAuth + ResolveTenant
- Scope ها (نمونه):
  - `esim_go.connection.view`
  - `esim_go.catalogue.sync`
  - `esim_go.order.view`

## Endpoints
- `GET /connections`
- `GET /products`
- `GET /products/{product}`
- `GET /orders`
- `GET /orders/{order}`
- `POST /sync` (payload: `type=catalogue|inventory`)
- `GET /openapi`

## Webhook
- `POST /api/v1/providers/esim-go/callback?connection_id=<id>`
  - بدون ApiKeyAuth (صرفاً HMAC)

> OpenAPI از طریق Filament API Docs Builder منتشر می‌شود.
