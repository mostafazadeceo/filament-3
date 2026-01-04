# Storefront Builder Governance

## Security
- Public endpoints are read-only.
- Admin changes require IAM permissions (`storebuilder.manage`, `storebuilder.publish`).

## Content lifecycle
- Draft and publish versions are immutable once published.
- Scheduled publish times must be validated against tenant timezone.

## SEO and redirects
- Redirects are tenant-scoped and should avoid loops.
- Sitemap should only include published pages.
