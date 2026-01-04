# IAM Upgrade ADR

## Context
IAM suite already provides tenancy, roles/permissions, access requests, audit, notifications, and basic impersonation. Enterprise requirements add JIT/PAM, secure impersonation, MFA, session management, protected actions, and SCIM/SSO scaffolds while keeping backward compatibility.

## Decisions
1. **Keep `filamat-iam-suite` as the core package** and extend it with new domain models/services/resources to avoid host-app coupling.
2. **Add new IAM-specific tables with `iam_` prefix** to avoid collisions and enable future pruning without affecting existing IAM tables.
3. **Preserve existing permissions/API endpoints** and add new permissions + `/api/v1/iam/*` endpoints for new enterprise features.
4. **JIT/PAM model**:
   - Eligibility table for privileged roles.
   - Request/approval workflow for activation.
   - Activation table with TTL; activations revoke roles on expiry.
5. **Impersonation model**:
   - Server-stored session record with signed token + TTL.
   - Mandatory reason + ticket/case id.
   - Restricted (read-only) mode by default; write allowed via explicit permission.
6. **Session management**:
   - Store session metadata in `iam_user_sessions` tied to tenant and user.
   - Revoke by deleting DB session (when driver supports it) + mark revoked.
7. **Protected actions**:
   - Step-up verification tokens in `iam_protected_action_tokens` with TTL.
   - Support password re-auth and MFA/TOTP.
8. **MFA**:
   - TOTP via `pragmarx/google2fa`.
   - WebAuthn scaffold (config + model placeholders).
9. **SCIM/SSO**:
   - Minimal SCIM endpoints + schema scaffolds, tenant-scoped and permission-gated.
   - OIDC/SAML adapters scaffolded behind config flags.
10. **Audit + security events**:
   - Add explicit audit/security events for privileged flows, sessions, and protected actions.

## Consequences
- New migrations must be registered in the IAM package service provider.
- New UI resources/pages registered via IAM plugin only.
- Existing behavior stays intact; new features default to disabled or feature-flagged.
- Tests and scenario runner must validate tenant isolation and audit coverage for privileged actions.

