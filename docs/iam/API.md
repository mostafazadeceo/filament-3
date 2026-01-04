# IAM API (v1)

## Base
- Base path: `/api/v1/iam/*`
- Headers:
  - `Authorization: Bearer <token>`
  - `X-Api-Key: <api_key>`
  - `X-Tenant-ID: <tenant_id>` (required for tenant-scoped operations)

## Scopes
- Middleware: `filamat-iam.scope:<scope>` resolves to `<scope>.view` or `<scope>.manage`.
- Suggested scopes: `iam`, `pam`, `session`, `mfa`, `scim`, `sso`.

## Invitations
- `POST /api/v1/iam/invitations`
  - Body: `email`, `roles[]`, `permissions[]`, `reason`, `expires_at`
- `POST /api/v1/iam/invitations/{id}/accept`
  - Body: `token`
- `POST /api/v1/iam/invitations/{id}/revoke`
  - Body: `reason`

## PAM / JIT
- `GET /api/v1/iam/privilege-eligibilities`
- `POST /api/v1/iam/privilege-eligibilities`
- `POST /api/v1/iam/privilege-requests`
  - Body: `user_id`, `role_id`, `requested_duration_minutes`, `reason`, `ticket_id`, `request_expires_at`
- `POST /api/v1/iam/privilege-requests/{id}/approve`
  - Body: `note`
- `POST /api/v1/iam/privilege-requests/{id}/deny`
  - Body: `note`
- `POST /api/v1/iam/privilege-activations`
  - Body: `user_id`, `role_id`, `request_id` (optional), `reason`, `ticket_id`, `expires_at`
- `POST /api/v1/iam/privilege-activations/{id}/revoke`
  - Body: `reason`

## Impersonation
- `POST /api/v1/iam/impersonations/start`
  - Body: `user_id`, `tenant_id`, `reason`, `ticket_id`, `ttl_minutes`, `restricted`
- `POST /api/v1/iam/impersonations/stop`

## Sessions
- `GET /api/v1/iam/sessions?user_id=`
- `POST /api/v1/iam/sessions/{id}/revoke`
  - Body: `reason`

## Protected Actions
- `POST /api/v1/iam/protected-actions/verify`
  - Body: `action`, `password` or `totp` or `backup_code`
  - Response: `token`, `expires_at`

## MFA
- `POST /api/v1/iam/mfa/totp/start`
- `POST /api/v1/iam/mfa/totp/confirm`
  - Body: `code`
- `POST /api/v1/iam/mfa/totp/reset`
  - Body: `reason`, plus `password` or `totp` or `backup_code` when step-up is required

## SCIM (Scaffold)
- `GET /api/v1/iam/scim/Users`
- `POST /api/v1/iam/scim/Users`
- `PATCH /api/v1/iam/scim/Users/{id}`
- `DELETE /api/v1/iam/scim/Users/{id}`
- `GET /api/v1/iam/scim/Groups`

## SSO (Scaffold)
- `GET /api/v1/iam/sso/providers`
- `POST /api/v1/iam/sso/oidc/callback`

## Notes
- All endpoints are tenant-scoped and permission-gated.
- `403` for insufficient permission or scope.
- Audit + security events are emitted for all privileged actions.
- For role/permission changes (`/api/v1/roles`, `/api/v1/permissions`), include `reason` in body or `X-Change-Reason` header.
