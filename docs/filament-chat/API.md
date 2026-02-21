# Filament Chat — API

## Base
`/api/v1/chat`

## احراز هویت
- `ApiKeyAuth`
- `ApiAuth`
- `ResolveTenant`
- `filamat-iam.scope:*`

## Endpoints
- `POST /api/v1/chat/connections/{connection}/test`
  - Scope: `chat.connection.view`
  - خروجی: `{ ok: true, result: {...} }`

- `POST /api/v1/chat/connections/{connection}/sync`
  - Scope: `chat.sync`
  - خروجی: `{ ok: true, synced: <count> }`

- `POST /api/v1/chat/connections/{connection}/users/{user}/sync`
  - Scope: `chat.sync`
  - خروجی: `{ ok: true, link_id: <id> }`

