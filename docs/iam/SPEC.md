# IAM Suite Specification (Enterprise)

## Scope
- Applies only to the hub.haida.co tenancy and panels.
- Extend `filamat-iam-suite` package; avoid host app edits unless strictly required.
- All UI and API paths are authorization-gated and tenant-scoped.

## Goals
- Enterprise-grade IAM with lifecycle, PAM/JIT, secure impersonation, session management, protected actions, MFA, and SCIM/SSO scaffolds.
- Preserve existing permission names and API contracts; add new capabilities only.

## Tenancy + Authorization
- Tenant resolution: `TenantContext` via middleware + Filament tenant.
- Data scoping: `BelongsToTenant` global scope (or explicit tenant_id filter).
- Authorization: `IamAuthorization::allows()` for UI + API checks.
- Teams: `spatie/laravel-permission` with `tenant_id` as team key.

## Domain Model (New)
- `iam_user_invitations`
  - Fields: tenant_id, email, roles[], permissions[], token_hash, status, reason, expires_at, accepted_at.
- `iam_privilege_eligibilities`
  - Fields: tenant_id, user_id, role_id, can_request, active, reason.
- `iam_privilege_requests`
  - Fields: tenant_id, user_id, role_id, requested_by_id, reason, ticket_id, duration, status, approvals.
- `iam_privilege_request_approvals`
  - Fields: request_id, approver_id, status, decided_at.
- `iam_privilege_activations`
  - Fields: tenant_id, user_id, role_id, activated_at, expires_at, revoked_at, reason.
- `iam_impersonation_sessions`
  - Fields: tenant_id, impersonator_id, impersonated_id, token_hash, reason, ticket_id, TTL, restricted.
- `iam_user_sessions`
  - Fields: session_id, tenant_id, user_id, ip, user_agent, last_activity_at, revoked_at.
- `iam_protected_action_tokens`
  - Fields: user_id, action, token_hash, verified_via, issued_at, expires_at, used_at.
- `iam_mfa_methods`
  - Fields: user_id, type (totp/webauthn), secret, backup_codes, enabled_at.

## Application Services (New)
- InviteUserService: create invitation, send notification, audit.
- UserLifecycleService: activate/suspend membership with reason.
- PrivilegeEligibilityService: manage eligible roles.
- PrivilegeElevationService: request/approve/activate/revoke JIT roles.
- ImpersonationService (extended): reason + ticket + TTL + audit.
- SessionService: record sessions, revoke, force logout.
- ProtectedActionService: step-up tokens for sensitive operations.
- MfaService: TOTP enrollment/verification + backup codes.
- ScimService (scaffold): user/group provisioning.
- SsoService (scaffold): OIDC/SAML adapters.

## UI (Filament)
- Resources/pages registered via `FilamatIamSuitePlugin`.
- New resources:
  - Invitations, Privilege Eligibilities, Privilege Requests, Privilege Activations
  - User Sessions, MFA Methods
- Persian labels for navigation, actions, and permissions.
- Impersonation banner: "در حال ورود به حساب کاربر X هستید" with stop action.

## API (New)
- Base path: `/api/v1/iam/*`
- Middleware: ApiKeyAuth + ApiAuth + ResolveTenant + filamat-iam.scope
- Endpoints for invitations, PAM, sessions, protected actions, MFA, SCIM/SSO scaffolds.

## Audit + Security Events
- Required audit events:
  - `user.invited`, `user.activated`, `user.suspended`
  - `pam.requested`, `pam.approved`, `pam.activated`, `pam.expired`, `pam.revoked`
  - `impersonation.started`, `impersonation.stopped`
  - `session.revoked`, `protected_action.verified`
  - `mfa.enabled`, `mfa.reset`, `mfa.backup_used`

## Notifications
- Use `haida/filament-notify-core` for IAM events.
- Optional: notify tenant owner on impersonation start/stop.

## Data + Indexing
- Index tenant_id, user_id, role_id, status, expires_at for all IAM tables.
- Avoid N+1 by eager loading relationships on listing pages.

## Backward Compatibility
- Keep existing permissions/API endpoints unchanged.
- Add new permissions for PAM, sessions, MFA, impersonation controls.

