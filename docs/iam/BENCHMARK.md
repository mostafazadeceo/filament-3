# IAM Benchmark (Best Practices)

## Access Control
- Deny-by-default permission checks; avoid role name hardcoding.
- Central capability registry + permission labels.
- Tenant-scoped authorization for every UI and API path.

## Session Security
- Regenerate session ID on privilege changes and impersonation start/stop.
- Track session metadata (ip/user-agent/last-activity) for visibility.
- Allow admin-driven session revoke and forced logout.

## Protected Actions
- Step-up verification (password/MFA) before sensitive operations.
- Short-lived step-up tokens, scoped to action + tenant.

## PAM / JIT
- Eligible privileged roles with explicit activation, reason, and TTL.
- Optional approvals for high-risk roles.
- Auto-expire activations and revoke roles.
- Audit every step (request, approval, activation, expiry).

## Impersonation
- Mandatory reason and ticket id.
- TTL enforced, auto-stop on expiry.
- Signed server-side token + session integrity.
- Restricted mode by default (read-only); write requires explicit permission.
- Visible banner at all times.

## MFA
- TOTP with backup codes.
- WebAuthn scaffold for future rollout.

## Enterprise
- OIDC/SAML adapters scaffolded behind config.
- SCIM endpoints with tenant scoping and least-privilege access.

## Audit & Governance
- Immutable audit events with hash-chain option.
- Reason fields required on role/permission changes.
- Approval workflow for privileged grants.

