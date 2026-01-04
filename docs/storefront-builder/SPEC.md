# Storefront Builder Specification

## Scope
Storefront Builder provides a headless CMS for storefront pages, blocks, menus, themes, and SEO metadata. It exposes public read-only endpoints for rendering.

## Domain entities
- StorePage, StorePageVersion
- StoreBlock
- StoreMenu, StoreMenuItem
- StoreTheme
- StoreRedirect

## Key behaviors
- Draft and publish flow with versioning.
- Scheduled publishing and SEO metadata per page.
- Public endpoints are read-only and tenant-scoped.
