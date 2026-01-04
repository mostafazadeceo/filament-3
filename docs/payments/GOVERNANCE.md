# Payments Governance

## Security
- Never store raw card data.
- Encrypt provider secrets at rest.
- Sanitize logs (no Authorization headers).

## Webhooks
- Validate signatures and reject replays.
- Persist webhook events for audit and troubleshooting.

## Refunds
- Refunds require IAM permission and audit trail.
- Use idempotency keys to avoid duplicates.

## Compliance
- Provider reconciliation should be run on a schedule per tenant.
