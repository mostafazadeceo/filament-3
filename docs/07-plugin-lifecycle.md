# Plugin Lifecycle (Platform Core)

## Overview
This document describes the platform-level plugin registry and lifecycle operations.
The registry tracks installed plugins, per-tenant enablement, and versioned migrations
without destructive data loss. All storage remains in UTC; UI calendars remain
Persian-first.

## Tables

### `plugin_registry`
Global registry of installed plugins.

Columns:
- `plugin_key` (unique)
- `name_fa`
- `description_fa`
- `version`
- `created_at_jalali` (string, manifest value)
- `status` (`installed`, `disabled`)
- `installed_at`
- `metadata` (json)
- timestamps

### `plugin_migrations`
Tracks versioned migrations per plugin.

Columns:
- `plugin_key`
- `version`
- `migration_batch`
- `direction` (`up` / `down`)
- `applied_at`
- `triggered_by_user_id` (nullable, ثبت‌کننده عملیات)
- `correlation_id` (nullable, برای ردیابی درخواست)

### `tenant_plugins`
Per-tenant enablement and time windows.

Columns:
- `tenant_id`
- `plugin_key`
- `enabled`
- `starts_at` / `ends_at`
- `limits` (json)
- timestamps

## Manifest contract
Each plugin provides a manifest with:
- `name_fa`
- `description_fa`
- `version`
- `created_at_jalali` (e.g. `1404/10/09`)
- optional `meta` (json)

## Lifecycle operations

### Install
- Creates or updates the registry record.
- Stores manifest data and sets `installed_at` once.

### Enable / Disable
- `tenant_plugins` records are created or updated.
- `enabled` gates UI + API per tenant.

### Upgrade
- Validates the current version.
- Updates `plugin_registry.version`.
- Records a migration entry with `direction=up`.

### Rollback
- Validates the current version.
- Updates `plugin_registry.version`.
- Records a migration entry with `direction=down`.

## Notes
- No data deletion on disable; disable is a soft gate.
- Version checks protect from accidental cross-version upgrades.
- Enforcement of feature gates is handled by the Feature Gate package (PR-004).
