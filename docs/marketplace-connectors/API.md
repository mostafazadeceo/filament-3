# Marketplace Connectors API

Base path: `/api/v1/filament-marketplace-connectors`

## Endpoints
- `GET /connectors` (scope: `marketplace.connectors.manage`)
- `POST /connectors/{connector}/sync` (scope: `marketplace.connectors.sync`)
- `GET /openapi` (scope: `marketplace.connectors.manage`)

## Notes
- All endpoints require ApiKeyAuth, ApiAuth, ResolveTenant.
- Sync jobs are queued and idempotent per job id.
