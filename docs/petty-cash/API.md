# API ماژول تنخواه (v1)

## احراز هویت
- ApiKey + ApiAuth + ResolveTenant
- اسکوپ‌ها با `filamat-iam.scope` کنترل می‌شوند.

## مسیرها
- `GET /api/v1/petty-cash/funds`
- `POST /api/v1/petty-cash/funds`
- `GET /api/v1/petty-cash/funds/{fund}`
- `PUT /api/v1/petty-cash/funds/{fund}`
- `DELETE /api/v1/petty-cash/funds/{fund}`

- `GET /api/v1/petty-cash/categories`
- `POST /api/v1/petty-cash/categories`
- `GET /api/v1/petty-cash/categories/{category}`
- `PUT /api/v1/petty-cash/categories/{category}`
- `DELETE /api/v1/petty-cash/categories/{category}`

- `GET /api/v1/petty-cash/expenses`
- `POST /api/v1/petty-cash/expenses`
- `GET /api/v1/petty-cash/expenses/{expense}`
- `PUT /api/v1/petty-cash/expenses/{expense}`
- `DELETE /api/v1/petty-cash/expenses/{expense}`
- `POST /api/v1/petty-cash/expenses/{expense}/submit`
- `POST /api/v1/petty-cash/expenses/{expense}/approve`
- `POST /api/v1/petty-cash/expenses/{expense}/reject`
- `POST /api/v1/petty-cash/expenses/{expense}/post`

- `GET /api/v1/petty-cash/replenishments`
- `POST /api/v1/petty-cash/replenishments`
- `GET /api/v1/petty-cash/replenishments/{replenishment}`
- `PUT /api/v1/petty-cash/replenishments/{replenishment}`
- `DELETE /api/v1/petty-cash/replenishments/{replenishment}`
- `POST /api/v1/petty-cash/replenishments/{replenishment}/submit`
- `POST /api/v1/petty-cash/replenishments/{replenishment}/approve`
- `POST /api/v1/petty-cash/replenishments/{replenishment}/reject`
- `POST /api/v1/petty-cash/replenishments/{replenishment}/post`

- `GET /api/v1/petty-cash/settlements`
- `POST /api/v1/petty-cash/settlements`
- `GET /api/v1/petty-cash/settlements/{settlement}`
- `PUT /api/v1/petty-cash/settlements/{settlement}`
- `DELETE /api/v1/petty-cash/settlements/{settlement}`
- `POST /api/v1/petty-cash/settlements/{settlement}/submit`
- `POST /api/v1/petty-cash/settlements/{settlement}/approve`
- `POST /api/v1/petty-cash/settlements/{settlement}/post`

- `GET /api/v1/petty-cash/openapi`
