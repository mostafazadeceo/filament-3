# Commerce Core API

Base path: `/api/v1/filament-commerce-core`

## Authentication
All endpoints require:
- ApiKeyAuth
- ApiAuth
- ResolveTenant
- filamat-iam scope middleware

## Endpoints
- `GET /snapshots/catalog` (scope: `commerce.catalog.view`)
- `GET /snapshots/pricing` (scope: `commerce.pricing.view`)
- `GET /snapshots/inventory` (scope: `commerce.inventory.view`)
- `GET /openapi` (scope: `commerce.catalog.view`)

## Notes
- Responses are tenant-scoped.
- Use `openapi` for full schema details.
