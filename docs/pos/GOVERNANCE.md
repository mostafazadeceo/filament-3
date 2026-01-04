# POS Governance

## Security
- POS operations are permission-gated (`pos.use`, `pos.manage_cash`).
- Offline actions are restricted to allowed payment providers.

## Audit
- Cash movements and sales are recorded with actor metadata.
- Session open/close captures expected cash and variance.

## Offline policy
- Catalog/pricing is server-authoritative.
- Client-created sales are validated and accepted/rejected with reasons.
