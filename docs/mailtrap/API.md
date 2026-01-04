# API — Mailtrap

Base: `/api/v1/mailtrap`

## احراز هویت
- `ApiKeyAuth`
- `ApiAuth`
- `ResolveTenant`
- Scopeها: `mailtrap.*`

## مسیرها
- `GET /connections`
- `POST /connections`
- `GET /connections/{connection}`
- `PUT /connections/{connection}`
- `DELETE /connections/{connection}`

- `GET /inboxes`
- `POST /inboxes/sync`

- `GET /messages`
- `GET /messages/{message}`
- `GET /messages/{message}/body?refresh=1`
- `GET /messages/{message}/attachments?refresh=1`
- `GET /messages/{message}/attachments/{attachment}`

- `GET /domains`
- `POST /domains/sync`

- `GET /offers`
- `POST /offers`
- `GET /offers/{offer}`
- `PUT /offers/{offer}`
- `DELETE /offers/{offer}`

- `GET /openapi`

## OpenAPI
خروجی از: `/api/v1/mailtrap/openapi`

