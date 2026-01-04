# Marketplace Connectors Specification

## Scope
Marketplace connectors provide a framework for external marketplace integrations (Amazon SP-API, eBay Sell API) with token storage and sync jobs.

## Domain entities
- MarketplaceConnector
- MarketplaceToken
- MarketplaceSyncJob
- MarketplaceSyncLog
- MarketplaceRateLimit

## Key behaviors
- Tokens are stored per tenant and encrypted.
- Sync jobs run per connector and support dry-run execution.
- Rate limits and backoff are configurable per provider.
