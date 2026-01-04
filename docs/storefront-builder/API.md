# Storefront Builder API

## Admin API
Base path: `/api/v1/filament-storefront-builder`

Endpoints:
- `GET /openapi` (scope: `storebuilder.view`)

## Public endpoints
Base path: `/{public_prefix}` (default: `/storefront`)

- `GET /sitemap.xml`
- `GET /pages/{slug}`
- `GET /menus/{key}`
- `GET /blocks/{key}`
- `GET /theme`

## Notes
- Public routes are read-only and resolved by site context.
- Admin API requires ApiKeyAuth + ApiAuth + ResolveTenant.
