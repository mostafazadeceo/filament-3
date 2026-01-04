# API — filamat-iam-suite

## استانداردها
- مسیر پایه: `/api/v1/<module>/...` (طبق الگوی پروژه)
- Middleware عمومی: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, و `filamat-iam.scope:<scope>`
- Rate limit پیش‌فرض: `60,1` مگر اینکه در config تغییر داده شده باشد.

## اندپوینت‌ها
| Method | Path | Scope | Permission | Rate Limit | Payload |
| --- | --- | --- | --- | --- | --- |
| POST | /api/v1/notifications/send | notification.send | notification.send | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/wallets/{wallet}/credit | wallet | wallet | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/wallets/{wallet}/debit | wallet | wallet | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/wallets/{wallet}/holds | wallet | wallet | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/wallets/transfer | wallet | wallet | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/wallet-holds/{hold}/capture | wallet | wallet | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/wallet-holds/{hold}/release | wallet | wallet | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/invitations | user.view | user.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/invitations/{invitation} | user.view | user.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/invitations | user.invite | user.invite | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/invitations/{invitation}/accept | user.invite | user.invite | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/invitations/{invitation}/revoke | user.invite | user.invite | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/n8n/callback | automation.manage | automation.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/privilege-requests | pam.request | pam.request | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/privilege-requests/{requestModel}/approve | pam.approve | pam.approve | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/privilege-requests/{requestModel}/deny | pam.approve | pam.approve | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/privilege-activations | pam.activate | pam.activate | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/privilege-activations/{activation}/revoke | pam.revoke | pam.revoke | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/impersonations/start | iam.impersonate | iam.impersonate | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/impersonations/stop | iam.impersonate | iam.impersonate | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/sessions | session | session | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/sessions/{session}/revoke | session.revoke | session.revoke | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/protected-actions/verify | iam | iam | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/mfa/totp/start | mfa.manage | mfa.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/mfa/totp/confirm | mfa.manage | mfa.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/mfa/totp/reset | mfa.reset | mfa.reset | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/scim/Users | scim.view | scim.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/scim/Users | scim.manage | scim.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| PATCH | /api/v1/scim/Users/{id} | scim.manage | scim.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| DELETE | /api/v1/scim/Users/{id} | scim.manage | scim.manage | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/scim/Groups | scim.view | scim.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/sso/providers | sso.view | sso.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/sso/oidc/callback | sso.manage | sso.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /filamat-iam/impersonation/stop | [ASSUMPTION] | [ASSUMPTION] | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/webhooks/notification-plugin | [ASSUMPTION] | [ASSUMPTION] | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/webhooks/payment-provider | [ASSUMPTION] | [ASSUMPTION] | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |

## OpenAPI
- در صورت وجود مسیر `/openapi`، خروجی از Filament API Docs Builder منتشر می‌شود.
