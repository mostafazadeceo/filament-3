# Loyalty Club API

Base: `/api/v1/loyalty`

All endpoints require:
- `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`
- `filamat-iam.scope:<permission>`
- Throttle via `filament-loyalty-club.api.rate_limit`

## Endpoints
- `GET /customers` (scope: `loyalty.customer.view`)
- `GET /customers/{customer}` (scope: `loyalty.customer.view`)
- `POST /customers` (scope: `loyalty.customer.manage`)
- `PUT /customers/{customer}` (scope: `loyalty.customer.manage`)
- `GET /customers/{customer}/balances` (scope: `loyalty.customer.view`)
- `POST /events` (scope: `loyalty.event.ingest`)
- `GET /rewards` (scope: `loyalty.reward.view`)
- `POST /rewards/{reward}/redeem` (scope: `loyalty.reward.redeem`)
- `POST /coupons/validate` (scope: `loyalty.coupon.view`)
- `POST /coupons/redeem` (scope: `loyalty.coupon.redeem`)
- `POST /referrals` (scope: `loyalty.referral.manage`)
- `GET /referrals/{referral}` (scope: `loyalty.referral.view`)
- `GET /missions` (scope: `loyalty.mission.view`)
- `GET /missions/{mission}/progress` (scope: `loyalty.mission.view`)
- `GET /campaigns/offers` (scope: `loyalty.campaign.view`)
- `GET /openapi` (scope: `loyalty.view`)

## Example: Event Ingest
```
POST /api/v1/loyalty/events
X-Api-Key: <token>
Authorization: Bearer <token>

{
  "customer_id": 12,
  "type": "purchase_completed",
  "idempotency_key": "evt-2025-0001",
  "source": "orders",
  "payload": {
    "amount": 250000,
    "currency": "irr",
    "order_id": 88
  }
}
```

## Example: Redeem Reward
```
POST /api/v1/loyalty/rewards/5/redeem
{
  "customer_id": 12,
  "payload": {
    "idempotency_key": "redeem-5-12",
    "charity_name": "خیریه نمونه",
    "charity_reference": "charity-001"
  }
}
```

## Example: Coupon Validate
```
POST /api/v1/loyalty/coupons/validate
{
  "customer_id": 12,
  "code": "LC-ABC123",
  "amount": 500000
}
```

## OpenAPI
Spec endpoint: `/api/v1/loyalty/openapi`

`LoyaltyOpenApi::toArray()` is used by Filament API Docs Builder.
