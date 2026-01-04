# Baseline Conventions (M0)

This document summarizes project conventions discovered for panel plugins, IAM/permissions, notify-core triggers, and OpenAPI publishing.

## Panel plugin registration
- Plugins are wired in `app/Providers/Filament/AdminPanelProvider.php` and `app/Providers/Filament/TenantPanelProvider.php` via `$panel->plugins([...])`.
- Plugin classes implement `Filament\Contracts\Plugin` with `getId()`, `register(Panel $panel)`, `boot(Panel $panel)` (e.g. `packages/filament-restaurant-ops/src/FilamentRestaurantOpsPlugin.php`).
- Registration is explicit in both panels (admin + tenant), sometimes gated with `class_exists()`.

## IAM + permissions + tenancy
- Capabilities are registered in the package service provider using `CapabilityRegistryInterface` (see `packages/filament-petty-cash-ir/src/Support/PettyCashCapabilities.php`).
- Policies consistently use `IamAuthorization::allows()` and `IamAuthorization::resolveTenantFromRecord()` (e.g. `packages/filament-petty-cash-ir/src/Policies/Concerns/HandlesPettyCashPermissions.php`).
- Filament resources often extend `Filamat\IamSuite\Filament\Resources\IamResource` and set `protected static ?string $permissionPrefix = 'module.entity'` for automatic CRUD permission mapping (example: `packages/filament-workhub/src/Filament/Resources/ProjectResource.php`).
- Tenant scoping patterns:
  - Models use `Filamat\IamSuite\Support\BelongsToTenant` (global scope) and often a local `UsesTenant` trait to auto-fill `tenant_id` on create.
  - Services/controllers use `TenantContext::getTenant()` / `getTenantId()` and respect `TenantContext::shouldBypass()`.
- Filament page/widget gating uses `Filamat\IamSuite\Filament\Concerns\AuthorizesIam` and a static `$permission` property when relevant.

## Notify-core trigger usage
- Notifications use `Haida\FilamentNotify\Core\Support\Triggers\TriggerDispatcher` and are dispatched from services after domain events (example: `packages/providers-esim-go-core/src/Services/EsimGoWebhookService.php`).
- Pattern: resolve panel id from config, call `dispatchForEloquent($panelId, $record, $event)` and swallow exceptions to keep primary flows resilient.

## OpenAPI publishing conventions
- Each module defines `Support/*OpenApi::toArray()` describing `openapi`, `info`, and `paths` (example: `packages/filament-restaurant-ops/src/Support/RestaurantOpsOpenApi.php`).
- `OpenApiController` returns the array (often wrapped in `response()->json(...)`).
- Routes: `routes/api.php` uses `/api/v1/<module>/openapi` inside the module API group and typically requires scope permission (see `packages/filament-threecx/routes/api.php`).
- API middleware chain (common): `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, `throttle:<config rate>`.
- Filament API Docs Builder plugin is enabled in both panels via `FilamentApiDocsBuilderPlugin::make()`.

## Implications for loyalty module
- Provide a plugin class registered in both panels.
- Register capabilities + policies via package service provider.
- Use `IamResource` + `InteractsWithTenant` for Filament resources.
- Enforce tenant scoping with `BelongsToTenant` / `UsesTenant` and `TenantContext` in services/APIs.
- Publish OpenAPI via `/api/v1/loyalty/openapi` and include IAM scope middleware.
- Use `TriggerDispatcher` for campaign/points/reward notifications.
