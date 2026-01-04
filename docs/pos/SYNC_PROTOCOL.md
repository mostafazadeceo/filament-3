# POS Sync Protocol

## Overview
The POS sync protocol supports offline-first clients with snapshot, delta, and outbox upload flows. All endpoints are tenant-scoped.

## 1) Snapshot
`GET /api/v1/filament-pos/sync/snapshot`

Returns a baseline dataset for offline use (catalog, prices, taxes, promotions, registers). Response includes `cursor` for delta sync.

Example response:
```json
{
  "data": {
    "catalog": [],
    "prices": [],
    "inventory": [],
    "registers": [],
    "taxes": []
  },
  "cursor": "2026-01-01T12:00:00Z"
}
```

## 2) Delta
`GET /api/v1/filament-pos/sync/delta?cursor=...`

Returns changes since the cursor and a new cursor.

```json
{
  "changes": {
    "catalog": [],
    "prices": [],
    "inventory": []
  },
  "cursor": "2026-01-01T13:00:00Z"
}
```

## 3) Outbox upload
`POST /api/v1/filament-pos/sync/outbox`

Body:
```json
{
  "device_id": 10,
  "events": [
    {
      "event_type": "sale",
      "event_id": "sale-123",
      "idempotency_key": "pos-sale-123",
      "payload": { "items": [], "payments": [] }
    }
  ]
}
```

Response:
```json
{
  "accepted": [{"event_type":"sale","status":"processed"}],
  "rejected": [{"event_type":"sale","reason":"validation_failed"}]
}
```

## Conflict rules
- Server is authoritative for catalog, pricing, and inventory rules.
- Client is authoritative for locally created sales and cash events.
- Server validates uploads and returns `accepted` or `rejected` with reasons.

## Idempotency
- Every outbox event must include `idempotency_key`.
- Duplicates return `status: duplicate` without creating new records.
