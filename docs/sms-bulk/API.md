# SMS Bulk API

Base: `/api/v1/sms-bulk`

## Auth & Middleware
All endpoints use:
- `ApiKeyAuth`
- `ApiAuth`
- `ResolveTenant`
- `filamat-iam.scope:<scope>`
- throttle

## Endpoints

### Credit
- `GET /credit`

### Provider Connections
- `GET /provider-connections`
- `POST /provider-connections`
- `GET /provider-connections/{id}`
- `PUT /provider-connections/{id}`
- `DELETE /provider-connections/{id}`

### Phonebooks & Contacts
- `GET /phonebooks`
- `POST /phonebooks`
- `GET /phonebooks/{id}`
- `PUT /phonebooks/{id}`
- `DELETE /phonebooks/{id}`
- `GET /phonebooks/options`
- `POST /phonebooks/options`
- `PUT /phonebooks/options/{id}`
- `DELETE /phonebooks/options/{id}`
- `GET /phonebooks/contacts`
- `POST /phonebooks/contacts`
- `PUT /phonebooks/contacts/{id}`
- `DELETE /phonebooks/contacts/{id}`

### Imports
- `POST /imports/contacts`
- `GET /imports/{id}`

### Patterns
- `GET /patterns`
- `POST /patterns`
- `GET /patterns/{id}`
- `PUT /patterns/{id}`
- `DELETE /patterns/{id}`

### Drafts
- `GET /drafts`
- `POST /drafts`
- `GET /drafts/{id}`
- `PUT /drafts/{id}`
- `DELETE /drafts/{id}`

### Campaigns
- `GET /campaigns`
- `POST /campaigns`
- `GET /campaigns/{id}`
- `POST /campaigns/{id}/submit`
- `POST /campaigns/{id}/pause`
- `POST /campaigns/{id}/resume`
- `POST /campaigns/{id}/cancel`

### Reports
- `GET /reports/outbox`
- `GET /reports/inbox`
- `GET /reports/bulk/{campaignId}/recipients`
- `GET /reports/export/csv`

### Consent / Suppression
- `POST /optout`
- `POST /optin`

### OpenAPI
- `GET /openapi`

## Example Error Model
```json
{
  "data": null,
  "meta": {
    "status": false,
    "message": "Validation failed"
  }
}
```
