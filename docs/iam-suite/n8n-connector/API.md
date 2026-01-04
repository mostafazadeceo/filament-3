# N8N Connector API

## POST /api/v1/iam/n8n/callback
Inbound callback for AI reports and action proposals.

### Middleware
- `ApiKeyAuth`
- `ApiAuth`
- `ResolveTenant`
- `filamat-iam.scope:automation.manage`

### Headers (Option A: Static Token)
- `X-Api-Key`: API key
- `X-Tenant-ID`: tenant id
- `X-N8N-Token`: static token

### Headers (Option B: HMAC)
- `X-Api-Key`: API key
- `X-Tenant-ID`: tenant id
- `X-Filamat-Signature`: HMAC signature
- `X-Filamat-Timestamp`: UNIX timestamp
- `X-Filamat-Nonce`: random nonce

### Request (Report)
```json
{
  "connector_id": 12,
  "idempotency_key": "uuid",
  "correlation_id": "uuid",
  "title": "Risk Summary",
  "severity": "high",
  "report": {
    "markdown": "...",
    "findings": ["..."]
  }
}
```

### Request (Action Proposal)
```json
{
  "connector_id": 12,
  "idempotency_key": "uuid",
  "correlation_id": "uuid",
  "proposal": {
    "action_type": "user.suspend",
    "target": { "user_id": 42 },
    "reason": "...",
    "requires_approval": true
  }
}
```

### Responses
- `200 OK` with `{ "status": "ok" }`
- `401 Unauthorized` for invalid signature/token
- `409 Conflict` if `idempotency_key` already processed
- `422 Unprocessable` for missing tenant or payload
