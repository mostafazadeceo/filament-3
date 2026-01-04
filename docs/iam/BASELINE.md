# IAM Baseline (Current State)

## Scope
- Package: `packages/filamat-iam-suite`
- Panels: Admin + Tenant via `FilamatIamSuitePlugin`
- Tenancy: `TenantContext` + `BelongsToTenant` global scope
- Permissions: `spatie/laravel-permission` with teams enabled (`tenant_id`)

## Current Capabilities
- Tenants/organizations, membership (`tenant_user`)
- Roles/permissions/groups + permission overrides + templates
- Access requests (approve/deny) -> permission overrides (time-bound)
- Delegated admin scopes
- Audit logs + hash chain
- Security events + login/logout notifications
- Webhooks (notification + payment) + replay protection
- API keys + API scopes middleware
- OTP codes (adapter-verified)
- Wallet + subscriptions (shared with IAM suite)
- Basic impersonation (session-based)

## Tables (IAM package)
- `organizations`, `tenants`, `tenant_user`
- `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`
- `groups`, `group_user`, `group_role`, `group_permission`
- `permission_overrides`, `permission_templates`, `permission_snapshots`
- `access_requests`, `access_request_approvals`
- `delegated_admin_scopes`
- `audit_logs`, `security_events`
- `notifications`, `otp_codes`
- `api_keys`, `api_key_scopes`
- `webhooks`, `webhook_deliveries`, `webhook_nonces`
- `wallets`, `wallet_transactions`, `wallet_holds`
- `subscription_plans`, `subscriptions`
- `user_profiles`

## Flows (Existing)
- Tenancy: `ResolveTenant` middleware + `TenantContext` + `TenantScope`
- Authorization: `IamAuthorization::allows()` -> `AccessService`
- Access requests: `AccessRequestService::create/approve/deny` -> permission overrides
- Impersonation: session flags + `ImpersonationService` (super-admin only)
- OTP: `OtpService` generates/verifies; adapter verify option
- Audit: `AuditService` + `AuditableObserver` + hash chain
- API: `/api/v1/*` resources, `ApiKeyAuth` + `ApiAuth` + `ResolveTenant` + `ApiScope`

## Endpoints (Existing)
- REST resources: `/api/v1/tenants`, `/api/v1/users`, `/api/v1/roles`, `/api/v1/permissions`, `/api/v1/groups`
- Wallet: `/api/v1/wallets`, `/api/v1/transactions`, `/api/v1/wallet-holds`, credit/debit/transfer
- Subscriptions: `/api/v1/plans`, `/api/v1/subscriptions`
- Notifications: `/api/v1/notifications/send`
- Webhooks: `/api/v1/webhooks/notification-plugin`, `/api/v1/webhooks/payment-provider`
- Impersonation stop: `/filamat-iam/impersonation/stop`

## Pain Points / Gaps
- Impersonation lacks reason/TTL/signed token/guardrails and cross-tenant controls.
- No JIT/PAM workflow for privileged role elevation.
- No session management UI or session revocation endpoints.
- No MFA/TOTP or protected-action step-up.
- No enterprise SSO/SCIM scaffolding.
- Access requests are permission-override centric, not time-bound role activation.
- No explicit audit events for privileged access or protected actions.
- API base path is not `/api/v1/iam/*` per module convention.

