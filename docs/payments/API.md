# Payments API

Base path: `/api/v1/filament-payments`

## Endpoints
- `POST /intents` (scope: `payments.manage`)
- `GET /intents/{intent}` (scope: `payments.view`)
- `POST /webhooks/{provider}` (scope: `payments.webhooks.manage`)
- `GET /openapi` (scope: `payments.view`)

## Notes
- All endpoints require ApiKeyAuth, ApiAuth, ResolveTenant.
- Webhooks validate signatures and are idempotent.
