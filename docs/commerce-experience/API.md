# Commerce Experience API

Base path: `/api/v1/filament-commerce-experience`

## Endpoints
- `GET /reviews` (scope: `experience.reviews.view`)
- `GET /questions` (scope: `experience.reviews.view`)
- `POST /csat` (scope: `experience.csat.manage`)
- `POST /buy-now` (scope: `experience.buy_now.manage`)
- `GET /openapi` (scope: `experience.reviews.view`)

## Notes
- All endpoints require ApiKeyAuth, ApiAuth, ResolveTenant.
- CSAT survey creation dispatches notify-core events.
