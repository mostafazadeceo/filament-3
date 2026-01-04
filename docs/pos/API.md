# POS API

Base path: `/api/v1/filament-pos`

## Endpoints
- `GET /sync/snapshot` (scope: `pos.use`)
- `GET /sync/delta` (scope: `pos.use`)
- `POST /sync/outbox` (scope: `pos.use`)
- `POST /sales` (scope: `pos.use`)
- `GET /openapi` (scope: `pos.view`)

## Notes
- All endpoints require ApiKeyAuth, ApiAuth, ResolveTenant.
- Snapshot and delta are for offline clients.
- Outbox uploads are idempotent by `idempotency_key`.
