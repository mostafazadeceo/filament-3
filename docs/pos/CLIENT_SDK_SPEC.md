# POS Client SDK Specification

## Target clients
- Windows desktop POS
- Mobile POS (Android/iOS)

## Authentication
- Use ApiKey + ApiAuth token headers.
- Tenant context is resolved via ApiAuth and tenant-aware API key.

## Local storage schema (suggested)
- tables: products, prices, inventory, promotions, taxes, registers
- tables: outbox_events, sync_cursors, device_profile
- keep `idempotency_key` per event

## Sync flow
1. On first run: call `sync/snapshot` and persist data.
2. On reconnect: call `sync/delta?cursor=...`.
3. Upload outbox via `sync/outbox` and mark accepted events.

## Retry strategy
- Exponential backoff for network failures.
- Never retry events without idempotency keys.
- Maintain local queue ordering per device.

## Conflict handling
- Server authoritative for catalog, pricing, taxes, inventory rules.
- Client authoritative for created sales and cash events.
- If rejected, store rejection reason and prompt operator.

## Offline payments
- Use manual external terminal payment type.
- Persist terminal reference for reconciliation.
