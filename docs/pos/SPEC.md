# POS Specification

## Scope
POS package provides registers, cashier sessions, cash movements, sales capture, and offline sync with idempotent outbox uploads.

## Domain entities
- PosStore, PosRegister, PosDevice
- PosCashierSession (shift)
- PosCashMovement (open float, pay-in/out, cash drop, reconciliation)
- PosSale, PosSaleItem, PosSalePayment
- PosSyncCursor, PosOutbox

## Key behaviors
- Sessions open/close with expected cash and variance tracking.
- Sales support split payments and manual external terminal provider.
- Offline sync uses snapshot + delta + outbox upload with idempotency.

## Tenancy and authorization
- All records are tenant-scoped.
- Permissions: `pos.view`, `pos.use`, `pos.manage_register`, `pos.manage_cash`.
