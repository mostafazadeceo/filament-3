# IAM Governance

## Principles
- Deny-by-default access checks.
- Tenant-scoped operations only.
- Reason-required changes for sensitive actions.

## Role/Permission Changes
- Require reason for:
  - Role creation/update/delete
  - Permission overrides
  - Permission template application
- All changes audited in `audit_logs`.
- API can send reason via body `reason` or header `X-Change-Reason`.

## PAM / JIT
- Eligibility grants are explicit and tenant-scoped.
- Requests require `reason` and `ticket_id` for privileged roles.
- Approval required by default (`pam.approve`).
- Activation is time-bound; auto-expire revokes roles.
- Weekly digest for active/expiring activations (notify-core).

## Impersonation
- Requires permissions: `iam.impersonate` (and `iam.impersonate.cross_tenant` for cross-tenant).
- Reason + ticket id required; TTL enforced.
- Restricted mode by default; write access requires `iam.impersonate.write`.
- Audit events on start/stop; tenant owner notification optional.

## Protected Actions
- Step-up required for sensitive actions:
  - PAM activation
  - Impersonation start
  - MFA reset
- Step-up via password or TOTP/backup code.

## Sessions
- Admins can list sessions and revoke by user/tenant.
- Session revoke triggers forced logout (database sessions only).

## MFA
- TOTP enabled by default; backup codes issued on enrollment.
- MFA reset requires reason + audit.
