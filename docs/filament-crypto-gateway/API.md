# API — filament-crypto-gateway

## استانداردها
- مسیر پایه: `/api/v1/<module>/...` (طبق الگوی پروژه)
- Middleware عمومی: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, و `filamat-iam.scope:<scope>`
- Rate limit پیش‌فرض: `60,1` مگر اینکه در config تغییر داده شده باشد.

## اندپوینت‌ها
| Method | Path | Scope | Permission | Rate Limit | Payload |
| --- | --- | --- | --- | --- | --- |
| POST | /api/v1/crypto/invoices | crypto.invoices.manage | crypto.invoices.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/crypto/invoices/{invoice} | crypto.invoices.view | crypto.invoices.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/crypto/invoices/{invoice}/status | crypto.invoices.view | crypto.invoices.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/crypto/invoices/{invoice}/refresh | crypto.invoices.manage | crypto.invoices.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/crypto/payouts | crypto.payouts.manage | crypto.payouts.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/crypto/payouts/{payout} | crypto.payouts.view | crypto.payouts.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/crypto/payouts/{payout}/approve | crypto.payouts.approve | crypto.payouts.approve | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/crypto/payouts/{payout}/reject | crypto.payouts.approve | crypto.payouts.approve | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/crypto/payout-destinations | crypto.payout_destinations.view | crypto.payout_destinations.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/crypto/payout-destinations | crypto.payout_destinations.manage | crypto.payout_destinations.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/crypto/payout-destinations/{destination} | crypto.payout_destinations.view | crypto.payout_destinations.view | [ASSUMPTION] 60,1 | - |
| PUT | /api/v1/crypto/payout-destinations/{destination} | crypto.payout_destinations.manage | crypto.payout_destinations.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| DELETE | /api/v1/crypto/payout-destinations/{destination} | crypto.payout_destinations.manage | crypto.payout_destinations.manage | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/crypto/webhooks/{provider} | crypto.webhooks.manage | crypto.webhooks.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/crypto/rates | crypto.rates.view | crypto.rates.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/crypto/policy | crypto.fee_policies.view | crypto.fee_policies.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/crypto/health/providers | crypto.providers.manage | crypto.providers.manage | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/crypto/health/nodes | crypto.nodes.view | crypto.nodes.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/crypto/reconcile/run | crypto.reconcile.run | crypto.reconcile.run | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/crypto/openapi | crypto.invoices.view | crypto.invoices.view | [ASSUMPTION] 60,1 | - |

## OpenAPI
- در صورت وجود مسیر `/openapi`، خروجی از Filament API Docs Builder منتشر می‌شود.
