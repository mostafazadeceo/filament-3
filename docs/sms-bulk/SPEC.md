# SMS Bulk Module Specification

## Scope
`haida/filament-sms-bulk` adds a multi-tenant Bulk SMS + reseller management module for Filament v4.

## Tenancy & IAM
- All domain models use tenant scoping via `BelongsToTenant` and `TenantContext`.
- All Filament resources/pages are permission-gated by IAM scopes.
- API routes are protected by:
  - `ApiKeyAuth`
  - `ApiAuth`
  - `ResolveTenant`
  - `filamat-iam.scope:<scope>`
  - throttling

## Domain Model (table prefix: `sms_bulk_`)
- Provider connections and sender identities
- Suppression list and consent registry
- Phonebooks, options, contacts
- Draft groups and draft messages (multilingual JSON)
- Pattern templates
- Campaigns and campaign recipients
- Quiet hours, quotas, rate limits, routing policies
- Import/export jobs
- Webhook logs
- Audit logs

## Core Flows
- Campaign draft creation with:
  - message validation
  - cost estimation (provider-first, local fallback)
  - quota checks
  - approval-state decision
- Campaign enqueue:
  - suppression filtering
  - per-recipient queue records
  - chunk dispatch
- Chunk sending:
  - mode-aware send method
  - primary/fallback provider routing
  - runtime rate-limit
  - recipient status updates
- Report sync:
  - provider bulk recipient updates mapped into local recipient logs

## Compliance
- Opt-out and opt-in supported (API + jobs + services)
- Suppression override logged in audit
- Quiet-hours profile available for schedule policy integration

## i18n
- Persian-first labels under `resources/lang/fa`
- English + Arabic provided
- Draft/pattern names and bodies stored in JSON translation fields

## Security
- Provider token is encrypted (`encrypted` cast)
- Token is never returned in API responses
- Audit logging for high-risk actions (approval/suppression override/opt in-out)

## Reseller Coverage
Provider abstraction includes methods for:
- user management
- package listing
- number assign/unassign/list
- ticket list/create/reply/show

## Known Safe Defaults
- If provider pricing endpoint fails, local estimator is used.
- If no routing policy exists, campaign uses selected/primary connection only.
