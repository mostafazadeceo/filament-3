# AGENTS.md — Filament Project Rules for Codex

## Project North Star
Build production-grade, extensible Filament v4 modules and plugins for this multi-tenant SaaS with strong access control and Persian-first UX.

## Scope of work
- We work across the entire Filament product, not a single module.
- Each new chat is a new module request unless stated otherwise.
- Every module must follow the core project structure and integrate with existing IAM, roles/permissions, notifications, and API docs.
- Keep modules isolated; do not modify other packages/modules unless required for integration.

## Hard constraints
- PHP 8.4+ / Laravel 12+ / Filament v4.
- Multi-panel and multi-tenant safe; enforce tenant scoping everywhere.
- Authorization required for all UI (Filament) and API endpoints.
- Prefer package/plugin architecture; avoid editing host app unless explicitly requested.
- No surveillance features.
- Git operations only when explicitly requested; otherwise work in the local tree only.

## Project architecture (deep scan)
- Panels: Admin panel uses `App\Providers\Filament\AdminPanelProvider`; tenant panel exists in `app/Providers/Filament/TenantPanelProvider.php`.
- Tenancy/IAM: `filamat/filamat-iam-suite` (local package) provides tenant model, scopes, roles/permissions, API middleware, and capability registry.
- Permissions: `spatie/laravel-permission` with teams enabled (tenant_id as team key).
- Notifications: `haida/filament-notify-core` and channel packages (telegram, whatsapp, webpush, sms, bale).
- API docs: `zpmlabs/filament-api-docs-builder` is installed and used.
- Localization: Jalali/Hijri packages are present; date rendering uses Jalali where applicable.
- Active core modules (packages; non-exhaustive, see `packages/` for full list):
  - `packages/filament-accounting-ir`
  - `packages/filament-restaurant-ops`
  - `packages/filament-petty-cash-ir`
  - `packages/filament-payroll-attendance-ir`
  - `packages/filament-workhub`
  - `packages/filament-commerce-*`
  - `packages/filament-crypto-*`
  - `packages/filament-notify-*` (notification stack)

## Localization
- Use clear Persian labels for permissions, navigation, forms, and settings.

## Docs expectations
- For any major module or plugin, add docs under docs/<module>/:
  - SPEC.md
  - INSTALL.md
  - API.md

## Module standards (packages)
- Create each module as a Laravel package under `packages/<module>`.
- Package naming default: `haida/<module>` and namespace `Haida\<Module>`.
- Use `spatie/laravel-package-tools` and register migrations, config, translations, routes.
- Implement Filament v4 Panel Plugin with `getId()`, `register()`, `boot()`.
- Register resources/pages/widgets via the plugin (do not hardcode in host app).
- Add a table prefix per module (e.g., `<module>_`) and keep it consistent.
- Add path repository entry in root `composer.json` for new packages.

## Tenancy, roles, and authorization
- Use `Filamat\IamSuite\Support\BelongsToTenant` for global tenant scoping.
- Use `Filamat\IamSuite\Support\TenantContext` for tenant resolution in services and APIs.
- Use `Filamat\IamSuite\Support\IamAuthorization::allows()` in policies and API guards.
- Register permissions with the IAM capability registry (`CapabilityRegistry`).
- Provide Persian permission labels (use `PermissionLabels` pattern for fallback naming).
- Ensure subscription gating is satisfied when running tenant scenarios (IAM subscription enforcement is enabled by default).

## API conventions
- Base path: `/api/v1/<module>/...` per module.
- Middleware: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, plus `filamat-iam.scope:<scope>`.
- Rate limit via config (default 60,1) unless module requires different.
- Publish OpenAPI docs using Filament API Docs Builder (no custom doc systems unless required).

## Notifications
- Use `haida/filament-notify-core` for all notifications.
- Prefer TriggerDispatcher for action/eloquent triggers; avoid parallel notification systems.
- Keep notification rules and templates in the central notification module.

## Quality gates
- Add DB indexes intentionally (FKs + frequent filters like status, assignee, due_date, updated_at, tenant_id, project_id).
- Avoid N+1 queries; use eager loading on listing pages.
- Use transactions for money/ledger or critical state changes.
- Use tests for core domain behaviors (permissions, tenancy, wallet/billing, webhooks).
- Avoid global formatting across the repo; format only the module/package being changed.
- Ensure new package migrations are registered in the package service provider `->hasMigrations([...])`.
- When running migrations in production, use `php artisan migrate --force`.

## Run commands (if available)
- php artisan test
- ./vendor/bin/pint packages/<module>
- phpstan/larastan (if installed)

## Scenario validation
- Use `scripts/deep_scenario_runner.php` for deep end-to-end validation across tenants/modules.
- Keep the scenario runner idempotent and safe to re-run.

## Output style
- Small milestones; after each: summarize diffs + commands run + next steps.
