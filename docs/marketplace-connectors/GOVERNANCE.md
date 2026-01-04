# Marketplace Connectors Governance

## Security
- OAuth tokens and API secrets are encrypted at rest.
- Access requires `marketplace.connectors.manage` or `marketplace.connectors.sync`.

## Rate limits
- Per-provider rate limit configuration is required.
- Backoff and retry should respect provider policies.

## Data integrity
- Sync logs capture request/response summaries for audit.
