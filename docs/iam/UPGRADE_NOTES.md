# IAM Upgrade Notes

## New Tables
- `iam_user_invitations`
- `iam_privilege_eligibilities`
- `iam_privilege_requests`
- `iam_privilege_request_approvals`
- `iam_privilege_activations`
- `iam_impersonation_sessions`
- `iam_user_sessions`
- `iam_protected_action_tokens`
- `iam_mfa_methods`
- New lifecycle columns on `tenant_user`

## New Config Keys
- `features.pam`, `features.sessions`, `features.protected_actions`, `features.mfa`, `features.scim`, `features.sso`
- `governance.require_reason`, `governance.reason_header`
- `impersonation.require_reason`, `impersonation.require_ticket`, `impersonation.restricted_default`
- `pam.*`
- `pam.digest.*`
- `sessions.*`
- `protected_actions.*`
- `mfa.*`

## New Permissions
- `pam.view`, `pam.manage`, `pam.request`, `pam.approve`, `pam.activate`, `pam.revoke`
- `session.view`, `session.manage`, `session.revoke`
- `mfa.view`, `mfa.manage`, `mfa.reset`
- `iam.impersonate`, `iam.impersonate.cross_tenant`, `iam.impersonate.write`
- `scim.view`, `scim.manage`, `sso.view`, `sso.manage`

## Behavior Changes
- Impersonation requires reason + ticket id, enforces TTL, and defaults to restricted mode.
- Impersonation session token is validated to detect tampering.
- PAM flow introduces eligibility + requests + time-bound activation.
- Protected actions add step-up tokens for high-risk operations.
- Session management tracks and can revoke user sessions.
- MFA reset requires step-up when configured.

## Backward Compatibility
- Existing permission names and API endpoints remain unchanged.
- New features are additive and can be feature-flagged.

## Operational Notes
- For session revoke, ensure session driver is `database`.
- Run capability sync after deploy.
- Ensure queue workers process IAM notifications.
