# API — filament-loyalty-club

## استانداردها
- مسیر پایه: `/api/v1/<module>/...` (طبق الگوی پروژه)
- Middleware عمومی: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, و `filamat-iam.scope:<scope>`
- Rate limit پیش‌فرض: `60,1` مگر اینکه در config تغییر داده شده باشد.

## اندپوینت‌ها
| Method | Path | Scope | Permission | Rate Limit | Payload |
| --- | --- | --- | --- | --- | --- |
| GET | /api/v1/loyalty/customers | loyalty.customer.view | loyalty.customer.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/loyalty/customers/{customer} | loyalty.customer.view | loyalty.customer.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/loyalty/customers | loyalty.customer.manage | loyalty.customer.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| PUT | /api/v1/loyalty/customers/{customer} | loyalty.customer.manage | loyalty.customer.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/loyalty/customers/{customer}/balances | loyalty.customer.view | loyalty.customer.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/loyalty/events | loyalty.event.ingest | loyalty.event.ingest | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/loyalty/rewards | loyalty.reward.view | loyalty.reward.view | [ASSUMPTION] 60,1 | - |
| POST | /api/v1/loyalty/rewards/{reward}/redeem | loyalty.reward.redeem | loyalty.reward.redeem | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/loyalty/coupons/validate | loyalty.coupon.view | loyalty.coupon.view | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/loyalty/coupons/redeem | loyalty.coupon.redeem | loyalty.coupon.redeem | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| POST | /api/v1/loyalty/referrals | loyalty.referral.manage | loyalty.referral.manage | [ASSUMPTION] 60,1 | {"data":"..."} [ASSUMPTION] |
| GET | /api/v1/loyalty/referrals/{referral} | loyalty.referral.view | loyalty.referral.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/loyalty/missions | loyalty.mission.view | loyalty.mission.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/loyalty/missions/{mission}/progress | loyalty.mission.view | loyalty.mission.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/loyalty/campaigns/offers | loyalty.campaign.view | loyalty.campaign.view | [ASSUMPTION] 60,1 | - |
| GET | /api/v1/loyalty/openapi | loyalty.view | loyalty.view | [ASSUMPTION] 60,1 | - |

## OpenAPI
- در صورت وجود مسیر `/openapi`، خروجی از Filament API Docs Builder منتشر می‌شود.
