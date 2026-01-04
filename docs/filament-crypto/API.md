# API — پلتفرم کیف‌پول و درگاه رمزارز

## پایه
- Base: `/api/v1/crypto`
- احراز هویت: `ApiKeyAuth` + `ApiAuth`
- تننت: `ResolveTenant`
- دسترسی: `filamat-iam.scope:<permission>`

## Endpoints
- `POST /invoices`
- `GET /invoices/{invoice}`
- `GET /invoices/{invoice}/status`
- `POST /invoices/{invoice}/refresh`
- `POST /payouts`
- `GET /payouts/{payout}`
- `POST /payouts/{payout}/approve`
- `POST /payouts/{payout}/reject`
- `GET /payout-destinations`
- `POST /payout-destinations`
- `GET /payout-destinations/{destination}`
- `PUT /payout-destinations/{destination}`
- `DELETE /payout-destinations/{destination}`
- `POST /webhooks/{provider}`
- `GET /rates?from=&to=`
- `GET /policy`
- `GET /health/providers`
- `GET /health/nodes`
- `POST /reconcile/run`
- `GET /openapi`

## نمونه‌ها
### ایجاد فاکتور
`POST /api/v1/crypto/invoices`
```json
{
  "provider": "cryptomus",
  "order_id": "ORDER-1001",
  "amount": 10,
  "currency": "USDT",
  "to_currency": "USDT",
  "network": "TRC20",
  "is_payment_multiple": false,
  "lifetime": 1800,
  "accuracy_payment_percent": 1.0,
  "subtract": 0
}
```
پاسخ:
```json
{
  "data": {
    "id": 1,
    "provider": "cryptomus",
    "order_id": "ORDER-1001",
    "status": "unpaid",
    "is_final": false,
    "address": "TV...",
    "expires_at": "2026-01-10T10:00:00Z"
  }
}
```

### وضعیت فاکتور
`GET /api/v1/crypto/invoices/1/status`
```json
{
  "id": 1,
  "status": "paid",
  "is_final": true
}
```

### برداشت
`POST /api/v1/crypto/payouts`
```json
{
  "provider": "cryptomus",
  "order_id": "PAYOUT-1001",
  "amount": 5,
  "currency": "USDT",
  "network": "TRC20",
  "to_address": "TR..."
}
```

### تایید برداشت
`POST /api/v1/crypto/payouts/1/approve`
```json
{
  "note": "ok"
}
```

### ثبت مقصد برداشت (لیست سفید)
`POST /api/v1/crypto/payout-destinations`
```json
{
  "label": "Main Wallet",
  "address": "TR...",
  "currency": "USDT",
  "network": "TRC20",
  "status": "active"
}
```

### وبهوک
`POST /api/v1/crypto/webhooks/{provider}`
- بدنه خام (JSON یا فرم) ذخیره می‌شود.
- امضا و IP بررسی می‌گردد.

## نکات کلیدی
- `order_id` در هر تننت یکتا و idempotent است.
- وضعیت‌ها مطابق State Machine داخلی نگاشت می‌شوند.
- برای OpenAPI کامل: `GET /api/v1/crypto/openapi`.
- خروجی OpenAPI را می‌توان در Filament API Docs Builder ایمپورت کرد.
- برداشت‌ها ممکن است با وضعیت `pending_approval` برگردند و نیازمند تایید در UI باشند.
- آدرس مقصد برداشت باید در لیست سفید ثبت شده باشد.
